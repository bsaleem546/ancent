<?php

namespace App\Traits\Filters;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class InvoiceFilters extends QueryFilters
{
    protected $request;

    public function __construct(Request $request)
    {
        $this->request = $request;
        parent::__construct($request);
    }

    public function prepareQueryFilters()
    {
        return $this->builder->with('repair', 'company', 'user', 'customer', 'extraServices')
            ->select(['invoices.*', 'repairs.number AS number'])
            // Only the invoices for repairs that have been reviewed and that need invoicing
            // ->leftJoin('repairs', 'repairs.id', '=', 'invoices.repair_id')
            ->leftJoin('repairs', function ($join) {
                $join->on('repairs.id', '=', 'invoices.repair_id');
                $join->on('repairs.reviewed', '=', DB::raw("1"));
                $join->on('repairs.invoicing_needed', '=', DB::raw("1"));
            })
            // 'locations.name AS name', 'locations.street AS street',
            // 'locations.postal_code AS postal_code','locations.place AS place'])
            ->leftJoin('equipment', 'equipment.id', '=', 'repairs.equipment_id')
            // ->leftJoin('locations', 'repairs.location_id', '=', 'locations.id')
            // ->leftJoin('repair_scheduled_employees', 'repairs.id', '=', 'repair_scheduled_employees.repair_id');
            // We are not doing any filtering on customers or operators table but we might need to in the future
            // Just uncomment if we will need them
            // Customers from invoices or from equipment - right now it's from equipment
            ->leftJoin('customers', 'invoices.customer_id', '=', 'customers.id')
            // ->leftJoin('customers', 'equipment.customer_id', '=', 'customers.id')
            // ->leftJoin('operators', 'equipment.operator_id', '=', 'operators.id');
            // Only the invoices that have the pdf generated
            // ->where('invoices.invoice_pdf_generated', '=', 1);
            // Only the invoices that have been reviewed and that need invoicing
            // ->whereNull('repairs.id')->orWhere(function ($query) {
            ->where(function ($query) {
                return $query->where(function ($query) {
                    return $query->where('repairs.reviewed', '=', 1)
                        ->where('repairs.invoicing_needed', '=', 1);
                })->orWhere('invoices.repair_id', '=', 0)
                    ->orWhere('invoices.invoice_pdf_generated', '=', 1);
            });
        // ->where('invoices.repair_id', '=', 0)->orWhere(function ($query) {
        //     return $query->where('repairs.reviewed', '=', 1)
        //                 ->where('repairs.invoicing_needed', '=', 1);
        // });
    }

    public function prepareCustomerOperatorFilters()
    {
        $loggedUser = Auth::user();
        $customer_id = $loggedUser->userDetails->customer_id;
        $operator_id = $loggedUser->userDetails->operator_id;

        if ($customer_id != 0 && $operator_id != 0) {
            $operator = $loggedUser->userDetails->customer_operator_filter;
            if ($operator == 'OR') {
                $this->builder->where(function ($query) use ($customer_id, $operator_id) {
                    return $query->where('equipment.customer_id', '=', $customer_id)
                        ->orWhere('equipment.operator_id', '=', $operator_id);
                });
            } else {
                $this->builder->where(function ($query) use ($customer_id, $operator_id) {
                    return $query->where('equipment.customer_id', '=', $customer_id)
                        ->where('equipment.operator_id', '=', $operator_id);
                });
            }
        } else if ($customer_id != 0) {
            $this->builder->where('equipment.customer_id', $customer_id);

        } else if ($operator_id != 0) {
            $this->builder->where('equipment.operator_id', $operator_id);
        } else {
            // NOOP
        }

        return $this->builder;
    }

    // START filtering functions
    // Invoices
    public function invoice_number($term)
    {
        return $this->builder->where('invoices.invoice_number', 'LIKE', "%$term%");
    }

    public function customer_name($term)
    {
        return $this->builder->where('customers.name', 'LIKE', "%$term%");
    }

    // Between dates
    public function invoice_date($term)
    {
        $fromToArr = explode(",", $term);
        if (count($fromToArr) != 2) return;

        return $this->builder->whereBetween('invoices.invoice_date', $fromToArr);
    }

    // This is actually the repair order number
    public function offer_number($term)
    {
        return $this->builder->where('invoices.repair_id', '!=', 0)->where('repairs.number', 'LIKE', "%$term%");
    }

    // Between dates
    public function due_date($term)
    {
        $fromToArr = explode(",", $term);
        if (count($fromToArr) != 2) return;

        return $this->builder->whereBetween('invoices.due_date', $fromToArr);
    }

    // Between dates
    public function payment_date($term)
    {
        $fromToArr = explode(",", $term);
        if (count($fromToArr) != 2) return;

        return $this->builder->whereBetween('invoices.payment_date', $fromToArr);
    }
    // END REPAIRS
    // END filtering functions

    // START sorting functions
    public function sort_invoice_number($type = null)
    {
        return $this->builder->orderBy('invoices.invoice_number_year', (!$type || $type == 'asc') ? 'asc' : 'desc')
            ->orderBy('invoices.invoice_number_suff', (!$type || $type == 'asc') ? 'asc' : 'desc');
    }

    public function sort_customer_name($type = null)
    {
        return $this->builder->orderBy('customers.name', (!$type || $type == 'asc') ? 'asc' : 'desc');
    }

    public function sort_invoice_date($type = null)
    {
        return $this->builder->orderBy('invoices.invoice_date', (!$type || $type == 'asc') ? 'asc' : 'desc');
    }

    public function sort_offer_number($type = null)
    {
        return $this->builder->orderBy('repairs.number', (!$type || $type == 'asc') ? 'asc' : 'desc');
    }

    public function sort_due_date($type = null)
    {
        return $this->builder->orderBy('invoices.due_date', (!$type || $type == 'asc') ? 'asc' : 'desc');
    }

    public function sort_payment_date($type = null)
    {
        return $this->builder->orderBy('invoices.payment_date', (!$type || $type == 'asc') ? 'asc' : 'desc');
    }

    public function sort_total_with_vat($type = null)
    {
        return $this->builder->orderBy('invoices.total_with_vat', (!$type || $type == 'asc') ? 'asc' : 'desc');
    }
    // END sorting functions
}

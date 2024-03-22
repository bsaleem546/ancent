<?php

namespace App\Traits\Filters;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class EquipmentFilters extends QueryFilters
{
    protected $request;

    public function __construct(Request $request)
    {
        $this->request = $request;
        parent::__construct($request);
    }

    public function prepareQueryFilters()
    {
        $this->builder
            ->select(['equipment.*'])
            ->leftJoin('locations', 'equipment.location_id', '=', 'locations.id');
        // We are not doing any filtering on customers or operators table but we might need to in the future
        // Just uncomment if we will need them
        // ->leftJoin('customers', 'equipment.customer_id', '=', 'customers.id')
        // ->leftJoin('operators', 'equipment.operator_id', '=', 'operators.id');

        return $this->builder;
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
                    return $query->where('customer_id', '=', $customer_id)
                        ->orWhere('operator_id', '=', $operator_id);
                });
            } else {
                $this->builder->where(function ($query) use ($customer_id, $operator_id) {
                    return $query->where('customer_id', '=', $customer_id)
                        ->where('operator_id', '=', $operator_id);
                });
            }
        } else if ($customer_id != 0) {
            $this->builder->where('customer_id', $customer_id);

        } else if ($operator_id != 0) {
            $this->builder->where('operator_id', $operator_id);
        } else {
            // NOOP
        }

        return $this->builder;
    }

    // START filtering functions
    public function active($term)
    {
        if ($term) return $this->builder->where('equipment.active', $term);
        else return $this->builder;
    }

    public function maintenance_contract($term)
    {
        if ($term != "") return $this->builder->whereIn('equipment.maintenance_contract', explode(",", $term));
        else return $this->builder;
    }

    public function number($term)
    {
        return $this->builder->where('equipment.number', 'LIKE', "%$term%");
    }

    // Multiselect
    public function type_id($term)
    {
        return $this->builder->whereIn('equipment.type_id', explode(",", $term));
    }

    // Multiselect
    public function model($term)
    {
        return $this->builder->where('equipment.model', 'LIKE', "%$term%");
    }

    // Multiselect
    public function manufacture_year($term)
    {
        return $this->builder->whereIn('equipment.manufacture_year', explode(",", $term));
    }

    public function manufacture_no($term)
    {
        return $this->builder->where('equipment.manufacture_no', 'LIKE', "%$term%");
    }

    // Between dates
    public function next_uvv($term)
    {
        $fromToArr = explode(",", $term);
        if (count($fromToArr) != 2) return;

        return $this->builder->whereBetween('equipment.next_uvv', $fromToArr);
    }

    // Between dates
    public function next_checkup($term)
    {
        $fromToArr = explode(",", $term);
        if (count($fromToArr) != 2) return;

        return $this->builder->whereBetween('equipment.next_checkup', $fromToArr);
    }

    public function name($term)
    {
        return $this->builder->where('locations.name', 'LIKE', "%$term%");
    }

    public function street($term)
    {
        return $this->builder->where('locations.street', 'LIKE', "%$term%");
    }

    public function postal_code($term)
    {
        return $this->builder->where('locations.postal_code', 'LIKE', "$term%");
    }

    public function place($term)
    {
        return $this->builder->where('locations.place', 'LIKE', "%$term%");
    }
    // END filtering functions

    // START sorting functions
    public function sort_number($type = null)
    {
        return $this->builder->orderBy('number', (!$type || $type == 'asc') ? 'asc' : 'desc');
    }

    public function sort_type_id($type = null)
    {
        return $this->builder->orderBy('type_id', (!$type || $type == 'asc') ? 'asc' : 'desc');
    }

    public function sort_model($type = null)
    {
        return $this->builder->orderBy('model', (!$type || $type == 'asc') ? 'asc' : 'desc');
    }

    public function sort_manufacture_year($type = null)
    {
        return $this->builder->orderBy('manufacture_year', (!$type || $type == 'asc') ? 'asc' : 'desc');
    }

    public function sort_manufacture_no($type = null)
    {
        return $this->builder->orderBy('manufacture_no', (!$type || $type == 'asc') ? 'asc' : 'desc');
    }

    public function sort_next_uvv($type = null)
    {
        return $this->builder->orderBy('next_uvv', (!$type || $type == 'asc') ? 'asc' : 'desc');
    }

    public function sort_name($type = null)
    {
        return $this->builder->orderBy('locations.name', (!$type || $type == 'asc') ? 'asc' : 'desc');
    }

    public function sort_street($type = null)
    {
        return $this->builder->orderBy('locations.street', (!$type || $type == 'asc') ? 'asc' : 'desc');
    }

    public function sort_postal_code($type = null)
    {
        return $this->builder->orderBy('locations.postal_code', (!$type || $type == 'asc') ? 'asc' : 'desc');
    }

    public function sort_place($type = null)
    {
        return $this->builder->orderBy('locations.place', (!$type || $type == 'asc') ? 'asc' : 'desc');
    }
    // END sorting functions
}

<?php

namespace App\Traits\Filters;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class RepairFilters extends QueryFilters
{
    protected $request;

    public function __construct(Request $request)
    {
        $this->request = $request;
        parent::__construct($request);
    }

    public function prepareQueryFilters()
    {
        return $this->builder->with('equipment', 'invoice', 'scheduledEmployees', 'repairLocation', 'repairCustomer', 'repairOperator', 'rate', 'user', 'timeTracking', 'customerInvoicing', 'repairReplacements', 'company')
            ->select(['repairs.*', 'equipment.next_uvv', 'equipment.number as eqnumber', 'equipment.model', 'locations.name as lname', 'locations.street as lstreet', 'locations.postal_code as lpcode', 'locations.place as lplace'])->distinct('repairs.id')
            // 'locations.name AS name', 'locations.street AS street',
            // 'locations.postal_code AS postal_code','locations.place AS place'])
            ->leftJoin('equipment', 'equipment.id', '=', 'repairs.equipment_id')
            ->leftJoin('locations', 'repairs.location_id', '=', 'locations.id')
            ->leftJoin('repair_scheduled_employees', 'repairs.id', '=', 'repair_scheduled_employees.repair_id');
        // We are not doing any filtering on customers or operators table but we might need to in the future
        // Just uncomment if we will need them
        // ->leftJoin('customers', 'equipment.customer_id', '=', 'customers.id')
        // ->leftJoin('operators', 'equipment.operator_id', '=', 'operators.id');
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
    // REPAIRS
    public function active($term)
    {
        if ($term) return $this->builder->where('repairs.status', '!=', 'repair_not_done')
            ->where('equipment.active', $term)
            ->where('repairs.active', $term);
        else return $this->builder;
    }

    public function offer_needed($term)
    {
        return $this->builder->where('repairs.offer_needed', '=', "$term");
    }

    public function repair_number($term)
    {
        return $this->builder->where('repairs.number', 'LIKE', "%$term%");
    }

    public function description($term)
    {
        return $this->builder->where('repairs.work_description', 'LIKE', "%$term%");
    }

    public function estimation($term)
    {
        return $this->builder->where('repairs.estimation', 'LIKE', "%$term%");
    }

    // Multiselect
    public function user_id($term)
    {
        return $this->builder->whereIn('repairs.user_id', explode(",", $term));
    }

    // Multiselect
    public function status($term)
    {
        return $this->builder->whereIn('repairs.status', explode(",", $term));
    }

    // Between dates
    public function schedule_date($term)
    {
        $fromToArr = explode(",", $term);
        if (count($fromToArr) != 2) return;

        // DOES NOT WORK FOR SELECTED INTERVAL AS SAME DAY
        // return $this->builder->where(function ($query) use ($fromToArr) {
        //     return $query->whereBetween('repairs.exact_schedule_start', $fromToArr)
        //                 ->orWhereBetween('repairs.exact_schedule_end', $fromToArr)
        //                 ->orWhere(function ($query) use ($fromToArr) {
        //                     return $query->whereNull('repairs.exact_schedule_start')->whereNull('repairs.exact_schedule_end')
        //                                 ->whereBetween('repairs.rough_schedule_start', $fromToArr)
        //                                 ->orWhereBetween('repairs.rough_schedule_end', $fromToArr);
        //                     });
        // });

        // $query->where('start', '<=', $to)->where('end', '>=', $from)
        return $this->builder->where(function ($query) use ($fromToArr) {
            return $query->where('repairs.exact_schedule_start', '<=', $fromToArr[1])->where('repairs.exact_schedule_end', '>=', $fromToArr[0])
                ->orWhere(function ($query) use ($fromToArr) {
                    return $query->whereNull('repairs.exact_schedule_start')->whereNull('repairs.exact_schedule_end')
                        ->where('repairs.rough_schedule_start', '<=', $fromToArr[1])
                        ->where('repairs.rough_schedule_end', '>=', $fromToArr[0]);
                });
        });
    }
    // END REPAIRS

    // REPAIR SCHEDULED EMPLOYEES
    // Multiselect
    public function employees($term)
    {
        $terms = explode(",", $term);
        if (in_array('-1', $terms)) {
            $to_remove = array('-1');
            $terms = array_diff($terms, $to_remove);
            return $this->builder->where(function ($query) use ($terms) {
                return $query->whereNull('repair_scheduled_employees.employee_id')
                    ->orWhereIn('repair_scheduled_employees.employee_id', $terms);
            });
        } else {
            return $this->builder->whereIn('repair_scheduled_employees.employee_id', explode(",", $term));
        }
    }
    // END EMPLOYEES

    // EQUIPMENTS
    public function equipment_number($term)
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
    // END EQUIPMENTS

    // LOCATIONS
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
    // END LOCATIONS
    // END filtering functions

    // START sorting functions
    public function sort_repair_number($type = null)
    {
        return $this->builder->orderBy('repairs.number', (!$type || $type == 'asc') ? 'asc' : 'desc');
    }

    public function sort_status($type = null)
    {
        return $this->builder->orderBy('repairs.status', (!$type || $type == 'asc') ? 'asc' : 'desc');
    }

    public function sort_description($type = null)
    {
        return $this->builder->orderBy('repairs.work_description', (!$type || $type == 'asc') ? 'asc' : 'desc');
    }

    public function sort_estimation($type = null)
    {
        return $this->builder->orderBy('repairs.estimation', (!$type || $type == 'asc') ? 'asc' : 'desc');
    }

    public function sort_schedule_date($type = null)
    {
        return $this->builder->orderBy('repairs.exact_schedule_start', (!$type || $type == 'asc') ? 'asc' : 'desc')
            ->orderBy('repairs.rough_schedule_start', (!$type || $type == 'asc') ? 'asc' : 'desc');;
    }

    public function sort_equipment_number($type = null)
    {
        return $this->builder->orderBy('eqnumber', (!$type || $type == 'asc') ? 'asc' : 'desc');
    }

    public function sort_type_id($type = null)
    {
        return $this->builder->orderBy('equipment.type_id', (!$type || $type == 'asc') ? 'asc' : 'desc');
    }

    public function sort_model($type = null)
    {
        return $this->builder->orderBy('equipment.model', (!$type || $type == 'asc') ? 'asc' : 'desc');
    }

    public function sort_manufacture_year($type = null)
    {
        return $this->builder->orderBy('equipment.manufacture_year', (!$type || $type == 'asc') ? 'asc' : 'desc');
    }

    public function sort_manufacture_no($type = null)
    {
        return $this->builder->orderBy('equipment.manufacture_no', (!$type || $type == 'asc') ? 'asc' : 'desc');
    }

    public function sort_next_uvv($type = null)
    {
        return $this->builder->orderBy('equipment.next_uvv', (!$type || $type == 'asc') ? 'asc' : 'desc');
    }

    public function sort_next_checkup($type = null)
    {
        return $this->builder->orderBy('equipment.next_checkup', (!$type || $type == 'asc') ? 'asc' : 'desc');
    }

    // END LOCATIONS
    public function sort_name($type = null)
    {
        return $this->builder->orderBy('lname', (!$type || $type == 'asc') ? 'asc' : 'desc');
    }

    public function sort_street($type = null)
    {
        return $this->builder->orderBy('lstreet', (!$type || $type == 'asc') ? 'asc' : 'desc');
    }

    public function sort_postal_code($type = null)
    {
        return $this->builder->orderBy('lpcode', (!$type || $type == 'asc') ? 'asc' : 'desc');
    }

    public function sort_place($type = null)
    {
        return $this->builder->orderBy('lplace', (!$type || $type == 'asc') ? 'asc' : 'desc');
    }
    // END LOCATIONS
    // END sorting functions
}

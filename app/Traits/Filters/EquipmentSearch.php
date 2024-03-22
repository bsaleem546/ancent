<?php

namespace App\Traits\Filters;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class EquipmentSearch extends QueryFilters
{
    protected $request;

    public function __construct(Request $request)
    {
        $this->request = $request;
        parent::__construct($request);
    }

    public function prepareQueryFilters()
    {
        return $this->builder
            ->select(['equipment.*',
                'locations.name AS name', 'locations.street AS street',
                'locations.postal_code AS postal_code', 'locations.place AS place'])
            ->leftJoin('locations', 'equipment.location_id', '=', 'locations.id')
            ->leftJoin('customers', 'equipment.customer_id', '=', 'customers.id')
            ->leftJoin('operators', 'equipment.operator_id', '=', 'operators.id')
            ->leftJoin('equipment_manufacturer', 'equipment.manufacturer_id', '=', 'equipment_manufacturer.id')
            ->leftJoin('equipment_type', 'equipment.type_id', '=', 'equipment_type.id');
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
    // START EQUIPMENT
    public function number($term)
    {
        return $this->builder->where('number', 'LIKE', "%$term%");
    }

    // Input
    public function model($term)
    {
        return $this->builder->where('model', 'LIKE', "%$term%");
    }

    // Input
    public function manufacturer($term)
    {
        return $this->builder->where('equipment_manufacturer.description', 'LIKE', "%$term%");
    }

    // Input
    public function equipment_type($term)
    {
        return $this->builder->where('equipment_type.description', 'LIKE', "%$term%");
    }

    // Input
    public function manufacture_year($term)
    {
        return $this->builder->where('equipment.manufacture_year', "$term");
    }

    public function manufacture_no($term)
    {
        return $this->builder->where('equipment.manufacture_no', 'LIKE', "%$term%");
    }

    public function notes($term)
    {
        return $this->builder->where('equipment.notes', 'LIKE', "%$term%");
    }

    public function internal_notes($term)
    {
        return $this->builder->where('equipment.internal_notes', 'LIKE', "%$term%");
    }
    // END EQUIPMENT

    // LOCATION
    public function location_name($term)
    {
        return $this->builder->where('locations.name', 'LIKE', "%$term%");
    }

    public function location_contact_person($term)
    {
        return $this->builder->where('locations.contact_person', 'LIKE', "%$term%");
    }

    public function location_street($term)
    {
        return $this->builder->where('locations.street', 'LIKE', "%$term%");
    }

    public function location_postal_code($term)
    {
        return $this->builder->where('locations.postal_code', 'LIKE', "$term%");
    }

    public function location_place($term)
    {// Place = Town
        return $this->builder->where('locations.place', 'LIKE', "%$term%");
    }

    public function location_notes($term)
    {
        return $this->builder->where('locations.notes', 'LIKE', "%$term%");
    }

    public function location_internal_notes($term)
    {
        return $this->builder->where('locations.internal_notes', 'LIKE', "%$term%");
    }

    // CUSTOMER
    public function customer_name($term)
    {
        return $this->builder->where('customers.name', 'LIKE', "%$term%");
    }

    public function customer_contact_person($term)
    {
        return $this->builder->where('customers.contact_person', 'LIKE', "%$term%");
    }

    public function customer_street($term)
    {
        return $this->builder->where('customers.street', 'LIKE', "%$term%");
    }

    public function customer_postal_code($term)
    {
        return $this->builder->where('customers.postal_code', 'LIKE', "$term%");
    }

    public function customer_place($term)
    {// Place = Town
        return $this->builder->where('customers.place', 'LIKE', "%$term%");
    }

    public function customer_notes($term)
    {
        return $this->builder->where('customers.notes', 'LIKE', "%$term%");
    }

    public function customer_internal_notes($term)
    {
        return $this->builder->where('customers.internal_notes', 'LIKE', "%$term%");
    }

    // OPERATOR
    public function operator_name($term)
    {
        return $this->builder->where('operators.name', 'LIKE', "%$term%");
    }

    public function operator_contact_person($term)
    {
        return $this->builder->where('operators.contact_person', 'LIKE', "%$term%");
    }

    public function operator_street($term)
    {
        return $this->builder->where('operators.street', 'LIKE', "%$term%");
    }

    public function operator_postal_code($term)
    {
        return $this->builder->where('operators.postal_code', 'LIKE', "$term%");
    }

    public function operator_place($term)
    {// Place = Town
        return $this->builder->where('operators.place', 'LIKE', "%$term%");
    }

    public function operator_notes($term)
    {
        return $this->builder->where('operators.notes', 'LIKE', "%$term%");
    }

    public function operator_internal_notes($term)
    {
        return $this->builder->where('operators.internal_notes', 'LIKE', "%$term%");
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

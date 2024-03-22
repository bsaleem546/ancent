<?php

namespace App\Traits\Filters;

use Illuminate\Http\Request;

class UserFilters extends QueryFilters
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
            ->select(['users.id AS id', 'users.username AS username', 'users.email AS email',
                'user_details.first_name AS first_name', 'user_details.last_name AS last_name',
                'user_details.customer_id AS customer_id', 'customers.name AS customer',
                'user_details.operator_id AS operator_id', 'operators.name AS operator'])
            ->leftJoin('user_details', 'user_details.user_id', '=', 'users.id')
            ->leftJoin('customers', 'user_details.customer_id', '=', 'customers.id')
            ->leftJoin('operators', 'user_details.operator_id', '=', 'operators.id');
    }

    // START filtering functions
    public function username($term)
    {
        return $this->builder->where('username', 'LIKE', "%$term%");
    }

    public function last_name($term)
    {
        return $this->builder->where('user_details.last_name', 'LIKE', "%$term%");
    }

    public function first_name($term)
    {
        return $this->builder->where('user_details.first_name', 'LIKE', "%$term%");
    }

    public function customer($term)
    {
        return $this->builder->where('customers.name', 'LIKE', "%$term%");
    }

    public function operator($term)
    {
        return $this->builder->where('operators.name', 'LIKE', "%$term%");
    }
    // END filtering functions

    // START sorting functions
    public function sort_username($type = null)
    {
        return $this->builder->orderBy('users.username', (!$type || $type == 'asc') ? 'asc' : 'desc');
    }

    public function sort_last_name($type = null)
    {
        return $this->builder->orderBy('user_details.last_name', (!$type || $type == 'asc') ? 'asc' : 'desc');
    }

    public function sort_first_name($type = null)
    {
        return $this->builder->orderBy('user_details.first_name', (!$type || $type == 'asc') ? 'asc' : 'desc');
    }

    public function sort_customer($type = null)
    {
        return $this->builder->orderBy('customers.name', (!$type || $type == 'asc') ? 'asc' : 'desc');
    }

    public function sort_operator($type = null)
    {
        return $this->builder->orderBy('operators.name', (!$type || $type == 'asc') ? 'asc' : 'desc');
    }
    // END sorting functions
}

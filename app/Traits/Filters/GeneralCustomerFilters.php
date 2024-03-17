<?php

namespace App\Traits\Filters;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class GeneralCustomerFilters extends GeneralFilters
{
    protected $request;

    public function __construct(Request $request)
    {
        $this->request = $request;
        parent::__construct($request);
    }

    public function prepareCustomerOperatorFilters()
    {
        $loggedUser = Auth::user();
        $customer_id = $loggedUser->userDetails->customer_id;
        $operator_id = $loggedUser->userDetails->operator_id;

        if ($customer_id != 0) {
            $this->builder->where('id', $customer_id);
        } else {
            // NOOP
        }

        return $this->builder;
    }
}

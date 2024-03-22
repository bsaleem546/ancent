<?php

namespace App\Traits\Filters;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\Request;

class QueryFilters
{
    protected $request;
    protected $builder;

    public function __construct(Request $request)
    {
        $this->request = $request;
    }

    public function apply(Builder $builder)
    {
        $this->builder = $builder;
        // First join the tables (we do not use whereHas as it's way too slow and does not return data from related tables)
        $this->prepareQueryFilters();
        $this->prepareCustomerOperatorFilters();

        // Apply each filter if present in the filters from request body
        foreach ($this->filters() as $name => $value) {
            if (!method_exists($this, $name)) {
                continue;
            }
            if (strlen($value)) {
                $this->$name($value);
            } else {
                // Do not run the function if we did not enter a
                // sort or a filter string
                // $this->$name();
                continue;
            }
        }

        // Apply each sorter if present in the sorters from request body
        foreach ($this->sorters() as $name => $value) {
            // add sort_ in front of each sorting field name to differentiate between
            // sorter and filter functions
            $name = "sort_" . $name;
            if (!method_exists($this, $name)) {
                continue;
            }
            if (strlen($value)) {
                $this->$name($value);
            } else {
                $this->$name();
            }
        }
        return $this->builder;
    }

    public function filters()
    {
        return (isset($this->request->filters) && is_iterable($this->request->filters)) ? $this->request->filters : [];
    }

    public function sorters()
    {
        return (isset($this->request->sorters) && is_iterable($this->request->sorters)) ? $this->request->sorters : [];
    }

    public function prepareQueryFilters()
    {
        // Do nothing
        return $this->builder;
    }

    public function prepareCustomerOperatorFilters()
    {
        // Do nothing
        return $this->builder;
    }
}

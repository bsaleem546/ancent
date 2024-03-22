<?php

namespace App\Traits\Filters;

use Illuminate\Http\Request;

class GeneralFilters extends QueryFilters
{
    protected $request;

    public function __construct(Request $request)
    {
        $this->request = $request;
        parent::__construct($request);
    }

    // START filtering functions
    public function general($term)
    {
        return $this->builder->where(function ($query) use ($term) {
            return $query->where('name', 'LIKE', "%$term%")
                ->orWhere('street', 'LIKE', "%$term%")
                ->orWhere('postal_code', 'LIKE', "$term%")
                ->orWhere('place', 'LIKE', "%$term%");
        });
    }

    public function active($term)
    {
        if ($term) return $this->builder->where('active', $term);
        else return $this->builder;
    }
    // END filtering functions

    // START sorting functions
    public function sort_id($type = null)
    {
        return $this->builder->orderBy('id', (!$type || $type == 'asc') ? 'asc' : 'desc');
    }

    public function sort_name($type = null)
    {
        return $this->builder->orderBy('name', (!$type || $type == 'asc') ? 'asc' : 'desc');
    }

    public function sort_street($type = null)
    {
        return $this->builder->orderBy('street', (!$type || $type == 'asc') ? 'asc' : 'desc');
    }

    public function sort_postal_code($type = null)
    {
        return $this->builder->orderBy('postal_code', (!$type || $type == 'asc') ? 'asc' : 'desc');
    }

    public function sort_place($type = null)
    {
        return $this->builder->orderBy('place', (!$type || $type == 'asc') ? 'asc' : 'desc');
    }
    // END sorting functions
}

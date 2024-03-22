<?php

namespace App\Traits\Filters;

use Illuminate\Database\Eloquent\Builder;

trait Filterable
{
    public function scopeFilter($query, QueryFilters $filters)
    {
        return $filters->apply($query);
    }
}

<?php

namespace App\Http\Controllers;

use App\Http\Resources\UnitResource;
use App\Models\Unit;
use Illuminate\Http\Request;

class UnitsController extends Controller
{
    public function getAll(Request $request)
    {
        $query = Unit::query()->orderBy("name", "ASC");
        return UnitResource::collection($query->paginate($request->pagination["per_page"], ['*'], 'page', $request->pagination["current_page"]));
    }
}

<?php

namespace App\Http\Controllers;

use App\Http\Resources\EmployeeResource;
use App\Models\Employee;
use Illuminate\Http\Request;

class EmployeeController extends Controller
{
    public function getAll(Request $request)
    {
        $query = Employee::query()->orderBy("last_name", "ASC")->orderBy("first_name", "ASC");
        return EmployeeResource::collection($query
            ->paginate($request->pagination["per_page"], ['*'], 'page', $request->pagination["current_page"]));
    }
}

<?php

namespace App\Http\Controllers;

use App\Http\Resources\CompanyResource;
use App\Models\Company;
use Illuminate\Http\Request;

class CompanyController extends Controller
{
    public function index($perPage = 25, $currentPage = 1)
    {
        return CompanyResource::collection(Company::paginate($perPage, ['*'], 'page', $currentPage));
    }

    public function show($id)
    {
        return new CompanyResource(Company::findOrFail($id));
    }
}

<?php

namespace App\Http\Controllers;

use App\Http\Resources\ReplacementResource;
use App\Models\Replacement;
use Illuminate\Http\Request;

class ReplacementController extends Controller
{
    public function index()
    {
        $query = Replacement::query()->select('replacements.id', 'replacements.group_id', 'replacements.number_id', 'replacements.description', 'replacements.description2', 'replacements.price', 'replacements.discount', 'replacements.unit_id')->leftJoin('replacement_groups', 'replacements.group_id', '=', 'replacement_groups.id')->orderBy("replacement_groups.name", "ASC")->orderBy("replacements.description", "ASC")->get();
        return ReplacementResource::collection($query);
    }

    public function show($id)
    {
        return new ReplacementResource(Replacement::findOrFail($id));
    }

    public function getAllReplacementGroups()
    {
        $apiResponse = (object)[];
        $apiResponse->data = Replacement::getAllGroups();

        return response()->json($apiResponse, 200);
    }
}

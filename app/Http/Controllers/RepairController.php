<?php

namespace App\Http\Controllers;

use App\Http\Resources\RepairResource;
use App\Models\Repair;
use Illuminate\Http\Request;

class RepairController extends Controller
{
    public function showP(Request $request): RepairResource
    {
        return new RepairResource(Repair::findOrFail($request->id));
    }

    public function showMultiple(Request $request): RepairResource
    {
        $query = Repair::query();
        if (isset($request->repairs) && $request->repairs != "") {
            $query->whereIn('id', $request->repairs);
        }

        $repairs = $query->get();
        error_log("Repairs: " . json_encode(RepairResource::collection($repairs)));

        return RepairResource::collection($repairs);
    }
}

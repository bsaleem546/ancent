<?php

namespace App\Http\Controllers;

use App\Http\Resources\VatResource;
use App\Models\Vat;
use Illuminate\Http\Request;

class VatController extends Controller
{
    public function getAll(Request $request)
    {
        $query = Vat::query()->orderBy("from", "DESC");
        return VatResource::collection($query->paginate($request->pagination["per_page"], ['*'], 'page', $request->pagination["current_page"]));
    }

    public function store(Request $request)
    {
        $vat = new Vat;
        $vat->vat = $request->vat;
        $vat->from = $request->from;
        $vat->save();

        return new VatResource($vat);
    }

    public function show($id)
    {
        return new VatResource(Vat::findOrFail($id));
    }

    public function update(Request $request)
    {
        $vat = Vat::findOrFail($request->id);

        if (isset($request->vat)) $vat->vat = $request->vat;
        if (isset($request->from)) $vat->from = $request->from;

        $vat->save();

        return new VatResource($vat);
    }

    public function destroy($id)
    {
        $vat = Vat::findOrFail($id);

        if ($vat->delete()) {
            return new VatResource($vat);
        }
    }
}

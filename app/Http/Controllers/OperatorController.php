<?php

namespace App\Http\Controllers;

use App\Http\Resources\OperatorResource;
use App\Models\Operator;
use App\Traits\Filters\GeneralOperatorFilters;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Auth;

class OperatorController extends Controller
{
    private function operatorValidator(array $data)
    {
        $validator = Validator::make($data, [
            'name' => ['required', 'string', 'max:100'],
            // 'street' => ['required', 'string', 'max:100'],
            // 'postal_code' => ['required', 'string', 'max:10'],
            // 'place' => ['required', 'string', 'max:100'],
            // 'country' => ['required', 'string', 'max:50'],
            // 'contact_person' => ['required', 'string'],
            // 'phone' => ['sometimes', new PhoneNumber],
            // 'fax' => ['sometimes', new PhoneNumber],
            // 'email' => ['required',  'email'],
            // 'notes' => ['sometimes', 'string'],
            // 'internal_notes' => ['sometimes', 'string'],
            // 'active' => ['required', 'integer']
        ]);

        return $validator;
    }

    public function getAll(Request $request, GeneralOperatorFilters $filters)
    {
        return OperatorResource::collection(Operator::filter($filters)
            ->paginate($request->pagination["per_page"], ['*'], 'page', $request->pagination["current_page"]));
    }

    public function show($id)
    {
        return new OperatorResource(Operator::findOrFail($id));
    }

    public function update(Request $request)
    {
        $apiResponse = array(
            'success' => false
        );

        $validator = $this->operatorValidator($request->all());
        if ($validator->fails()) {
            $apiResponse['errors'] = $validator->messages();
            return $apiResponse;
        }

        if (isset($request->id) && $request->id != 0) {
            $operator = Operator::findOrFail($request->id);

            $operator->name = $request->name;

            if (isset($request->street)) $operator->street = $request->street;
            if (isset($request->postal_code)) $operator->postal_code = $request->postal_code;
            if (isset($request->place)) $operator->place = $request->place;
            if (isset($request->country)) $operator->country = $request->country;
            if (isset($request->contact_person)) $operator->contact_person = $request->contact_person;
            if (isset($request->phone)) $operator->phone = $request->phone;
            if (isset($request->fax)) $operator->fax = $request->fax;
            if (isset($request->email)) $operator->email = $request->email;
            if (isset($request->gsm_email)) $operator->gsm_email = $request->gsm_email;
            if (isset($request->notes)) $operator->notes = $request->notes;
            if (isset($request->internal_notes) && Auth::user()->hasPermissionTo('access_internal_notes')) $operator->internal_notes = $request->internal_notes;
            if (isset($request->active)) $operator->active = $request->active;

            $operator->save();

            return new OperatorResource($operator);
        } else {
            $apiResponse = array(
                'success' => false
            );
            $apiResponse['errors'] = "operator id missing";

            return $apiResponse;
        }
    }

    public function store(Request $request)
    {
        $apiResponse = array(
            'success' => false
        );

        $validator = $this->operatorValidator($request->all());
        if ($validator->fails()) {
            $apiResponse['errors'] = $validator->messages();
            return response()->json($apiResponse, 422);
        }

        $operator = new Operator;

        $operator->name = $request->name;

        if (isset($request->street)) $operator->street = $request->street;
        if (isset($request->postal_code)) $operator->postal_code = $request->postal_code;
        if (isset($request->place)) $operator->place = $request->place;
        if (isset($request->country)) $operator->country = $request->country;
        if (isset($request->contact_person)) $operator->contact_person = $request->contact_person;
        if (isset($request->phone)) $operator->phone = $request->phone;
        if (isset($request->fax)) $operator->fax = $request->fax;
        if (isset($request->email)) $operator->email = $request->email;
        if (isset($request->gsm_email)) $operator->gsm_email = $request->gsm_email;
        if (isset($request->notes)) $operator->notes = $request->notes;
        if (isset($request->internal_notes) && Auth::user()->hasPermissionTo('access_internal_notes')) $operator->internal_notes = $request->internal_notes;
        if (isset($request->active)) $operator->active = $request->active;

        $operator->save();

        return new OperatorResource($operator);
    }

    public function destroy($id)
    {
        $operator = Operator::findOrFail($id);
        $operator->delete();
        return new OperatorResource($operator);
    }
}

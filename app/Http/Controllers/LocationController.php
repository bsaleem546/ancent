<?php

namespace App\Http\Controllers;

use App\Http\Resources\LocationResource;
use App\Models\Location;
use App\Traits\Filters\GeneralLocationFilters;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;

class LocationController extends Controller
{
    private function locationValidator(array $data)
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
            // 'gsm_email' => ['required',  'email'],
            // 'notes' => ['sometimes', 'string'],
            // 'internal_notes' => ['sometimes', 'string'],
            // 'special_features' => ['sometimes', 'string'],
            // 'active_travel_cost' => ['required', 'integer'],
            // 'travel_costs' => ['required_if:active_travel_cost, 1'],
            // 'active_km' => ['required', 'integer'],
            // 'km_costs' => ['required_if:active_km, 1'],
            // 'active' => ['required', 'integer'],
            // 'is_gsm' => ['required', 'integer']
        ]);

        return $validator;
    }

    public function getAll(Request $request, GeneralLocationFilters $filters)
    {
        return LocationResource::collection(Location::filter($filters)
            ->paginate($request->pagination["per_page"], ['*'], 'page', $request->pagination["current_page"]));
    }

    public function show($id)
    {
        return new LocationResource(Location::findOrFail($id));
    }

    public function update(Request $request)
    {
        $apiResponse = array(
            'success' => false
        );

        $validator = $this->locationValidator($request->all());
        if ($validator->fails()) {
            $apiResponse['errors'] = $validator->messages();
            return $apiResponse;
        }

        if (isset($request->id) && $request->id != 0) {
            $location = Location::findOrFail($request->id);

            $location->name = $request->name;

            if (isset($request->street)) $location->street = $request->street;
            if (isset($request->postal_code)) $location->postal_code = $request->postal_code;
            if (isset($request->place)) $location->place = $request->place;
            if (isset($request->country)) $location->country = $request->country;
            if (isset($request->contact_person)) $location->contact_person = $request->contact_person;
            if (isset($request->phone)) $location->phone = $request->phone;
            if (isset($request->fax)) $location->fax = $request->fax;
            if (isset($request->email)) $location->email = $request->email;
            if (isset($request->gsm_email)) $location->gsm_email = $request->gsm_email;
            if (isset($request->notes)) $location->notes = $request->notes;
            if (isset($request->internal_notes) && Auth::user()->CP('access_internal_notes')) $location->internal_notes = $request->internal_notes;
//            if (isset($request->internal_notes) && Auth::user()->hasPermissionTo('access_internal_notes')) $location->internal_notes = $request->internal_notes;
            if (isset($request->special_features)) $location->special_features = $request->special_features;
            if (Auth::user()->CPA(['access_prices_offer'])) {
//                if (Auth::user()->hasAllPermissions(['access_prices_offer'])) {
                if (isset($request->active_travel_cost)) $location->active_travel_cost = $request->active_travel_cost;
                if (isset($request->travel_costs)) $location->travel_costs = $request->travel_costs;
                if (isset($request->active_km)) $location->active_km = $request->active_km;
                if (isset($request->km_costs)) $location->km_costs = $request->km_costs;
            }
            if (isset($request->active)) $location->active = $request->active;
            if (isset($request->is_gsm)) $location->is_gsm = $request->is_gsm;

            $location->save();

            return new LocationResource($location);
        } else {
            $apiResponse = array(
                'success' => false
            );
            $apiResponse['errors'] = "location id missing";

            return $apiResponse;
        }
    }

    public function store(Request $request)
    {
        $apiResponse = array(
            'success' => false
        );

        $validator = $this->locationValidator($request->all());
        if ($validator->fails()) {
            $apiResponse['errors'] = $validator->messages();
            return response()->json($apiResponse, 422);
        }

        $location = new Location;

        $location->name = $request->name;

        if (isset($request->street)) $location->street = $request->street;
        if (isset($request->postal_code)) $location->postal_code = $request->postal_code;
        if (isset($request->place)) $location->place = $request->place;
        if (isset($request->country)) $location->country = $request->country;
        if (isset($request->contact_person)) $location->contact_person = $request->contact_person;
        if (isset($request->phone)) $location->phone = $request->phone;
        if (isset($request->fax)) $location->fax = $request->fax;
        if (isset($request->email)) $location->email = $request->email;
        if (isset($request->gsm_email)) $location->gsm_email = $request->gsm_email;
        if (isset($request->notes)) $location->notes = $request->notes;
        if (isset($request->internal_notes) && Auth::user()->CP('access_internal_notes')) $location->internal_notes = $request->internal_notes;
//        if (isset($request->internal_notes) && Auth::user()->hasPermissionTo('access_internal_notes')) $location->internal_notes = $request->internal_notes;
        if (isset($request->special_features)) $location->special_features = $request->special_features;
        if (Auth::user()->CPA(['access_prices_offer'])) {
//            if (Auth::user()->hasAllPermissions(['access_prices_offer'])) {
            if (isset($request->active_travel_cost)) $location->active_travel_cost = $request->active_travel_cost;
            if (isset($request->travel_costs)) $location->travel_costs = $request->travel_costs;
            if (isset($request->active_km)) $location->active_km = $request->active_km;
            if (isset($request->km_costs)) $location->km_costs = $request->km_costs;
        }
        if (isset($request->active)) $location->active = $request->active;
        if (isset($request->is_gsm)) $location->is_gsm = $request->is_gsm;

        $location->save();

        return new LocationResource($location);
    }

    public function destroy($id)
    {
        $location = Location::findOrFail($id);

        $location->delete();
        return new LocationResource($location);
    }
}

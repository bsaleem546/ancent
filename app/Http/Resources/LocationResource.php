<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;
use Illuminate\Support\Facades\Auth;

class LocationResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array
     */
    public function toArray($request)
    {
        return [
            'id' => $this->id,
            'name' => $this->name,
            'street' => $this->street,
            'postal_code' => $this->postal_code,
            'place' => $this->place,
            'country' => $this->country,
            'contact_person' => $this->contact_person,
            'phone' => $this->phone,
            'fax' => $this->fax,
            'email' => $this->email,
            'gsm_email' => $this->gsm_email,
            'notes' => (isset($this->notes)) ? $this->notes : "",
            // Only  send the internal_notes if the requesting user has access to internal notes
            $this->mergeWhen(Auth::user()->hasPermissionTo('access_internal_notes'), [
                'internal_notes' => (isset($this->internal_notes)) ? $this->internal_notes : "",
            ]),
            'special_features' => (isset($this->special_features)) ? $this->special_features : "",
            $this->mergeWhen(Auth::user()->hasAllPermissions(['access_prices_offer']), [
                'active_travel_cost' => $this->active_travel_cost,
                'travel_costs' => $this->travel_costs,
                'active_km' => $this->active_km,
                'km_costs' => $this->km_costs,
            ]),
            'active' => $this->active,
            'is_gsm' => $this->is_gsm
        ];
    }
}

<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;
use Illuminate\Support\Facades\Auth;

class OperatorResource extends JsonResource
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
            $this->mergeWhen(Auth::user()->CP('access_internal_notes'), [
                'internal_notes' => (isset($this->internal_notes)) ? $this->internal_notes : "",
            ]),
            'active' => $this->active
        ];
    }
}

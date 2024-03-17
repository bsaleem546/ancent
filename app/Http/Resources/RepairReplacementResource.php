<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;
use Illuminate\Support\Facades\Auth;

class RepairReplacementResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array
     */
    public function toArray($request)
    {
        $loggedUser = Auth::user();
        return [
            'id' => $this->id,
            'check' => false,
            'repair_id' => $this->repair_id,
            'position' => $this->position,
            'count' => $this->count,
            'number_id' => $this->number_id,
            'description' => $this->description,
            'price' => $this->when($loggedUser->hasPermissionTo('access_prices_offer'), $this->price),
            'discount' => $this->when($loggedUser->hasPermissionTo('access_prices_offer'), $this->discount),
            'replacement_discount' => $this->when($loggedUser->hasPermissionTo('access_prices_offer'), $this->replacement_discount),
            'unit' => $this->unit && $this->unit->name ? $this->unit->name : null,
            'unit_id' => $this->unit && $this->unit->id ? $this->unit->id : null,
        ];
    }
}

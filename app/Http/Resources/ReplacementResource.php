<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;
use Illuminate\Support\Facades\Auth;

class ReplacementResource extends JsonResource
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
            'part_id' => $this->id,
            'selected' => false,
            'check' => false,
            'group_id' => $this->group_id,
            'number_id' => $this->number_id,
            'description' => $this->description,
            'description2' => $this->description2,
            'price' => $this->price,
            'discount' => $this->discount,
            // Send the price every time
            // 'price' => $this->when($loggedUser->CP('access_prices_offer'), $this->price),
            // 'discount' => $this->when($loggedUser->CP('access_prices_offer'), $this->discount),
            'unit' => $this->unit && $this->unit->name ? $this->unit->name : null,
            'unit_id' => $this->unit && $this->unit->id ? $this->unit->id : null,

        ];
    }
}

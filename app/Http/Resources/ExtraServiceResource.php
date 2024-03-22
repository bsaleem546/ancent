<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;
use Illuminate\Support\Facades\Auth;

class ExtraServiceResource extends JsonResource
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
            'invoice_id' => $this->invoice_id,
            'position' => $this->position,
            'name' => $this->name,
            'count' => $this->count,
            'unit' => $this->unit && $this->unit->name ? $this->unit->name : null,
            'unit_id' => $this->unit && $this->unit->id ? $this->unit->id : null,
            'discount' => $this->discount,
            'price' => $this->price,
        ];
    }
}

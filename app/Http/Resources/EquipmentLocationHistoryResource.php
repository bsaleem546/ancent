<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class EquipmentLocationHistoryResource extends JsonResource
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
            // 'equipment_id' => $this->equipment_id,
            'location_id' => $this->location_id,
            'location_name' => $this->location && $this->location->name ? $this->location->name : "",

            'customer_id' => $this->customer_id,
            'operator_id' => $this->operator_id,

            'location' => $this->location,
            'customer' => $this->customer,
            'operator' => $this->operator,

            'from' => $this->from,
            'to' => $this->to
        ];
    }
}

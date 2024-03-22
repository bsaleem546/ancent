<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;
use Illuminate\Support\Facades\Auth;

class EquipmentMinResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @param \Illuminate\Http\Request $request
     * @return array
     */
    public function toArray($request)
    {
        return [
            'id' => $this->id,
            'number' => $this->number,
            $this->mergeWhen(Auth::user()->CP('read locations'), [
                'location' => $this->location && $this->location->name ? $this->location->name : null,
            ])
        ];
    }
}

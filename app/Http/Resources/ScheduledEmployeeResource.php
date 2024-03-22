<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class ScheduledEmployeeResource extends JsonResource
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
            'repair_id' => $this->repair_id,
            'employee_id' => $this->employee_id,
            'employee' => new EmployeeResource($this->employee),
        ];
    }
}

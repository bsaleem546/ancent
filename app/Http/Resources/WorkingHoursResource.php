<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class WorkingHoursResource extends JsonResource
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
            'check' => false,
            'repair_id' => $this->repair_id,
            'employee_id' => $this->employee_id,
            'employee_name' => $this->employee->first_name." ".$this->employee->last_name,
            'employee' => new EmployeeResource($this->employee),
            'worked_on' => $this->worked_on,
            'description' => $this->description,
            'work_h' => $this->work_h,
            'work_min' => $this->work_min,
            'travel_h' => $this->travel_h,
            'travel_min' => $this->travel_min
        ];
    }
}

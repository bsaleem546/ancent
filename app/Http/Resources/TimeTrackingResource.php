<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class TimeTrackingResource extends JsonResource
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

            'work_time_from_h' => $this->work_time_from_h,
            'work_time_from_m' => $this->work_time_from_m,
            'work_time_till_h' => $this->work_time_till_h,
            'work_time_till_m' => $this->work_time_till_m,

            'drive_to_h' => $this->drive_to_h,
            'drive_to_m' => $this->drive_to_m,
            'drive_to_km' => $this->drive_to_km,

            'drive_from_h' => $this->drive_from_h,
            'drive_from_m' => $this->drive_from_m,
            'drive_from_km' => $this->drive_from_km
        ];
    }
}

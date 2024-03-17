<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class CustomerInvoicingResource extends JsonResource
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

            'unit_cost' => $this->unit_cost,
            'total_cost' => $this->total_cost,

            'work_h' => $this->work_h,
            'work_min' => $this->work_min,
            'internal' => ($this->internal ? $this->internal : false),
            'driving_time' => ($this->driving_time ? $this->driving_time : false)
        ];
    }
}

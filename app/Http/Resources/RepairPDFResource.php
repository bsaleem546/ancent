<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;
use Illuminate\Support\Facades\Auth;

class RepairPDFResource extends JsonResource
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
            'equipment_id' => $this->equipment_id,
            'company_id' => $this->company_id,
            'location_id' => $this->location_id,
            'customer_id' => $this->customer_id,
            'operator_id' => $this->operator_id,
            'user_id' => $this->user_id,
            'number' => $this->number,

            // Read repairs right required to see these fields
            $this->mergeWhen($loggedUser->hasPermissionTo('read repairs'), [
                'status' => $this->status,
                'work_description' => $this->work_description,
                'offer_needed' => $this->offer_needed,
            ]),

            // Only  send the internal_notes if the requesting user has access to internal notes
            $this->mergeWhen($loggedUser->hasAllPermissions(['access_internal_notes', 'read repairs']), [
                'internal_notes' => (isset($this->internal_notes)) ? $this->internal_notes : "",
            ]),
            
            // Read repair_details right required
            $this->mergeWhen($loggedUser->hasPermissionTo('read repair_details'), [
                'repair_details_added' => $this->repair_details_added,
                'repair_date' => $this->repair_date,
                'estimation' => $this->estimation,
                'rough_schedule_start' => $this->rough_schedule_start,
                'rough_schedule_end' => $this->rough_schedule_end,
                'exact_schedule_start' => $this->exact_schedule_start,
                'exact_schedule_end' => $this->exact_schedule_end,
                'reviewed' => $this->reviewed,
                'repair_blocked' => $this->repair_blocked,
                'hours_of_operations' => number_format($this->hours_of_operations, 2, ".", ""),
                'rate_id' => $this->rate_id,
                'rate' => $this->rate,
                'invoicing_needed' => $this->invoicing_needed,
                'active_travel_cost' => $this->active_travel_cost,
                'travel_costs' => $this->when($loggedUser->hasPermissionTo('access_prices_offer'), number_format($this->travel_costs, 2, ".", "")),
                'travel_cost_factor' => $this->when($loggedUser->hasPermissionTo('access_prices_offer'), number_format($this->travel_cost_factor, 3, ".", "")),
                'active_km' => $this->active_km,
                'km_costs' => $this->when($loggedUser->hasPermissionTo('access_prices_offer'), number_format($this->km_costs, 2, ".", "")),
                'active_per_km' => $this->active_per_km,
                'km' => number_format($this->km, 2, ".", ""),
                'costs_per_km' => $this->when($loggedUser->hasPermissionTo('access_prices_offer'), number_format($this->costs_per_km, 2, ".", "")),
                'active' => $this->active,
            ]),
            
            'created_at' => date('Y-m-d',strtotime($this->created_at)),

            // Do not send this info for now, keep it light
            'equipment' => new Equipment($this->equipment),
            'company' => $this->company,
            'user' => new User($this->user),
            'pdf_user' => new User($loggedUser),
            $this->mergeWhen($loggedUser->hasPermissionTo('read repair_details'), [
                'location' => $this->when($loggedUser->hasPermissionTo('read locations'), new LocationResource($this->repairLocation)),
                'customer' => $this->when($loggedUser->hasPermissionTo('read customers'), new CustomerResource($this->repairCustomer)),
                'operator' => $this->when($loggedUser->hasPermissionTo('read operators'), new OperatorResource($this->repairOperator)),
                'time_tracking' => TimeTrackingResource::collection($this->timeTracking),
                'customer_invoicing' => CustomerInvoicingResource::collection($this->customerInvoicing),
                'repair_replacements' => RepairReplacementResource::collection($this->repairReplacements),
            ]),
        ];
    }
}

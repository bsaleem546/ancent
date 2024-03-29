<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;
use Illuminate\Support\Facades\Auth;

class RepairResource extends JsonResource
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
            'related_repair' => $this->related_repair,
            'related_repair_id' => $this->related_repair_id,

            // Read repairs right required to see these fields
            $this->mergeWhen($loggedUser->CP('read repairs'), [
                'status' => $this->status,
                'work_description' => $this->work_description,
                'offer_needed' => $this->offer_needed,
                'had_offer_needed' => $this->had_offer_needed,
            ]),

            // Only  send the internal_notes if the requesting user has access to internal notes
            $this->mergeWhen($loggedUser->CPA(['access_internal_notes', 'read repairs']), [
                'internal_notes' => (isset($this->internal_notes)) ? $this->internal_notes : "",
            ]),

            // Read repair_details right required
            $this->mergeWhen($loggedUser->CP('read repair_details'), [
                'repair_details_added' => $this->repair_details_added,
                'had_offer_needed' => $this->had_offer_needed,
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
                'rate' => $this->when($loggedUser->CP('access_prices_offer'), $this->rate),
                'invoicing_needed' => $this->invoicing_needed,
                'active_travel_cost' => $this->active_travel_cost,
                'travel_costs' => $this->when($loggedUser->CP('access_prices_offer'), number_format($this->travel_costs, 2, ".", "")),
                'travel_cost_factor' => $this->when($loggedUser->CP('access_prices_offer'), number_format($this->travel_cost_factor, 3, ".", "")),
                'active_km' => $this->active_km,
                'km_costs' => $this->when($loggedUser->CP('access_prices_offer'), number_format($this->km_costs, 2, ".", "")),
                'active_per_km' => $this->active_per_km,
                'km' => number_format($this->km, 2, ".", ""),
                'costs_per_km' => $this->when($loggedUser->CP('access_prices_offer'), number_format($this->costs_per_km, 2, ".", "")),
                'active' => $this->active,
            ]),

            'created_at' => date('Y-m-d',strtotime($this->created_at)),

            // Do not send this info for now, keep it light
            'equipment' => new Equipment($this->equipment),
            'company' => $this->company,
            'user' => new User($this->user),
            'pdf_user' => new User($loggedUser),
            $this->mergeWhen($loggedUser->CP('read repair_details'), [
                'scheduled_employees' => $this->scheduledEmployees,
                'scheduled_employees_res' => ScheduledEmployeeResource::collection($this->scheduledEmployees),
                'location' => $this->when($loggedUser->CP('read locations'), new LocationResource($this->repairLocation)),
                'lct' => $this->when($loggedUser->CP('read locations'), new LocationResource($this->repairLocation)),
                'l' => $this->when($loggedUser->CP('read locations'), new LocationResource($this->repairLocation)),
                // 'location' => new LocationResource($this->repairLocation),
                // 'l' => $this->repairLocation,
                // 'lct' => new LocationResource($this->repairLocation),
                'customer' => $this->when($loggedUser->CP('read customers'), new CustomerResource($this->repairCustomer)),
                // 'customer' => new CustomerResource($this->repairCustomer),
                'cdsc' => ($this->repairCustomer && $this->repairCustomer->discount ? $this->repairCustomer->discount : 0),
                'operator' => $this->when($loggedUser->CP('read operators'), new OperatorResource($this->repairOperator)),
                // 'operator' => new OperatorResource($this->repairOperator),
                'invoice' => new InvoiceResource($this->invoice),
                'invoice_clean' => $this->invoice,
                'time_tracking' => TimeTrackingResource::collection($this->timeTracking),
                'customer_invoicing' => CustomerInvoicingResource::collection($this->customerInvoicing),
                'repair_replacements' => RepairReplacementResource::collection($this->repairReplacements),
            ]),
        ];
    }
}

<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;
use Illuminate\Support\Facades\Auth;

class EquipmentResource extends JsonResource
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
            'number' => $this->number,
            'manufacture_year' => $this->manufacture_year,
            'manufacture_no' => $this->manufacture_no,
            'next_uvv' => $this->next_uvv,
            'next_checkup' => $this->next_checkup,
            'model' => $this->model,
            'type_id' => $this->type_id,
            // 'type' => $this->equipmentType,
            $this->mergeWhen(is_object($this->equipmentType), ['type' => (is_object($this->equipmentType) && isset($this->equipmentType->description)) ? $this->equipmentType->description : ""]),
            'manufacturer_id' => $this->manufacturer_id,
            // 'manufacturer' => $this->equipmentManufacturer,
            $this->mergeWhen(is_object($this->equipmentManufacturer), ['manufacturer' => (is_object($this->equipmentManufacturer) && isset($this->equipmentManufacturer->description)) ? $this->equipmentManufacturer->description : ""]),
            
            'location_id' => $this->location_id,
            $this->mergeWhen(Auth::user()->hasPermissionTo('read locations'), [
                'location' => $this->location,
                'equipment_location_history' => EquipmentLocationHistoryResource::collection($this->equipmentLocationHistory),
            ]),
            
            'customer_id' => $this->customer_id,
            $this->mergeWhen(Auth::user()->hasPermissionTo('read customers'), [
                'customer' => $this->customer,
            ]),
            
            'operator_id' => $this->operator_id,
            $this->mergeWhen(Auth::user()->hasPermissionTo('read operators'), [
                'operator' => $this->operator,
            ]),
            'notes' => (isset($this->notes)) ? $this->notes : "",
            // Only  send the internal_notes if the requesting user has access to internal notes
            $this->mergeWhen(Auth::user()->hasPermissionTo('access_internal_notes'), [
                'internal_notes' => (isset($this->internal_notes)) ? $this->internal_notes : "",
            ]),
            'active' => $this->active,
            'maintenance_contract' => $this->maintenance_contract,
            // $this->mergeWhen(Auth::user()->hasPermissionTo('read repairs'), [
                'repairs' => RepairResource::collection($this->repairs),
            // ]),

            // Location section
            // $this->mergeWhen(Auth::user()->hasPermissionTo('read locations') && is_object($this->location), [
            //     'l.name' => (is_object($this->location) && isset($this->location->name)) ? $this->location->name : "",
            //     'l.street' => (is_object($this->location) && isset($this->location->street)) ? $this->location->street : "",
            //     'l.postal_code' => (is_object($this->location) && isset($this->location->postal_code)) ? $this->location->postal_code : "",
            //     'l.place' => (is_object($this->location) && isset($this->location->place)) ? $this->location->place : "",
            //     'l.country' => (is_object($this->location) && isset($this->location->country)) ? $this->location->country : "",
            //     'l.contact_person' => (is_object($this->location) && isset($this->location->contact_person)) ? $this->location->contact_person : "",
            //     'l.phone' => (is_object($this->location) && isset($this->location->phone)) ? $this->location->phone : "",
            //     'l.fax' => (is_object($this->location) && isset($this->location->fax)) ? $this->location->fax : "",
            //     'l.email' => (is_object($this->location) && isset($this->location->email)) ? $this->location->email : "",
            //     'l.gsm_email' => (is_object($this->location) && isset($this->location->gsm_email)) ? $this->location->gsm_email : "",
            //     'l.notes' => (is_object($this->location) && isset($this->location->notes)) ? $this->location->notes : "",
            //     'l.internal_notes' => $this->when(Auth::user()->hasPermissionTo('access_internal_notes'), (is_object($this->location) && isset($this->location->internal_notes)) ? $this->location->internal_notes : ""),
            //     'l.special_features' => (is_object($this->location) && isset($this->location->special_features)) ? $this->location->special_features : "",
            //     'l.active_travel_cost' => (is_object($this->location) && isset($this->location->active_travel_cost)) ? $this->location->active_travel_cost : 0,
            //     'l.travel_costs' => (is_object($this->location) && isset($this->location->travel_costs)) ? $this->location->travel_costs : 0.00,
            //     'l.active_km' => (is_object($this->location) && isset($this->location->active_km)) ? $this->location->active_km : 0,
            //     'l.km_costs' => (is_object($this->location) && isset($this->location->km_costs)) ? $this->location->km_costs : 0.00,
            //     'l.active_per_km' => (is_object($this->location) && isset($this->location->active_per_km)) ? $this->location->active_per_km : 0,
            //     'l.active' => (is_object($this->location) && isset($this->location->active)) ? $this->location->active : 0,
            //     'l.is_gsm' => (is_object($this->location) && isset($this->location->is_gsm)) ? $this->location->is_gsm : 0
            // ]),
            
            // Customer section
            // $this->mergeWhen(Auth::user()->hasPermissionTo('read customers') && is_object($this->customer), [
            //     'c.name' => (is_object($this->customer) && isset($this->customer->name)) ? $this->customer->name : "",
            //     'c.street' => (is_object($this->customer) && isset($this->customer->street)) ? $this->customer->street : "",
            //     'c.postal_code' => (is_object($this->customer) && isset($this->customer->postal_code)) ? $this->customer->postal_code : "",
            //     'c.place' => (is_object($this->customer) && isset($this->customer->place)) ? $this->customer->place : "",
            //     'c.country' => (is_object($this->customer) && isset($this->customer->country)) ? $this->customer->country : "",
            //     'c.contact_person' => (is_object($this->customer) && isset($this->customer->contact_person)) ? $this->customer->contact_person : "",
            //     'c.phone' => (is_object($this->customer) && isset($this->customer->phone)) ? $this->customer->phone : "",
            //     'c.fax' => (is_object($this->customer) && isset($this->customer->fax)) ? $this->customer->fax : "",
            //     'c.email' => (is_object($this->customer) && isset($this->customer->email)) ? $this->customer->email : "",
            //     'c.notes' => (is_object($this->customer) && isset($this->customer->notes)) ? $this->customer->notes : "",
            //     'c.internal_notes' => $this->when(Auth::user()->hasPermissionTo('access_internal_notes'), (is_object($this->customer) && isset($this->customer->internal_notes)) ? $this->customer->internal_notes : ""),
            //     'c.id_rw' => (is_object($this->customer) && isset($this->customer->id_rw)) ? $this->customer->id_rw : 0,
            //     'c.vat_id' => (is_object($this->customer) && isset($this->customer->vat_id)) ? $this->customer->vat_id : 0,
            //     'c.invoice_prefix' => (is_object($this->customer) && isset($this->customer->invoice_prefix)) ? $this->customer->invoice_prefix : "",
            //     'c.accounting_area' => (is_object($this->customer) && isset($this->customer->accounting_area)) ? $this->customer->accounting_area : "",
            //     'c.active' => (is_object($this->customer) && isset($this->customer->active)) ? $this->customer->active : 0
            // ]),
            
            // Operator section
            // $this->mergeWhen(Auth::user()->hasPermissionTo('read operators') && is_object($this->operator), [
            //     'o.name' => (is_object($this->operator) && isset($this->operator->name)) ? $this->operator->name : "",
            //     'o.street' => (is_object($this->operator) && isset($this->operator->street)) ? $this->operator->street : "",
            //     'o.postal_code' => (is_object($this->operator) && isset($this->operator->postal_code)) ? $this->operator->postal_code : "",
            //     'o.place' => (is_object($this->operator) && isset($this->operator->place)) ? $this->operator->place : "",
            //     'o.country' => (is_object($this->operator) && isset($this->operator->country)) ? $this->operator->country : "",
            //     'o.contact_person' => (is_object($this->operator) && isset($this->operator->contact_person)) ? $this->operator->contact_person : "",
            //     'o.phone' => (is_object($this->operator) && isset($this->operator->phone)) ? $this->operator->phone : "",
            //     'o.fax' => (is_object($this->operator) && isset($this->operator->fax)) ? $this->operator->fax : "",
            //     'o.email' => (is_object($this->operator) && isset($this->operator->email)) ? $this->operator->email : "",
            //     'o.gsm_email' => (is_object($this->operator) && isset($this->operator->gsm_email)) ? $this->operator->gsm_email : "",
            //     'o.notes' => (is_object($this->operator) && isset($this->operator->notes)) ? $this->operator->notes : "",
            //     'o.internal_notes' => $this->when(Auth::user()->hasPermissionTo('access_internal_notes'), (is_object($this->operator) && isset($this->operator->internal_notes)) ? $this->operator->internal_notes : ""),
            //     'o.active' => (is_object($this->operator) && isset($this->operator->active)) ? $this->operator->active : 0
            // ])
        ];
    }
}

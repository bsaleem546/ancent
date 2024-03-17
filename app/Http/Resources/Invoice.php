<?php
/**
 * Repair rsource class used to send minimal data to the UI for RepairCollections
 * DO NOT confuse with repairResource that is used to return a single Repair's data to the UI
 * And that contains all the repair details
 *
 */
namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;
use Illuminate\Support\Facades\Auth;

class Invoice extends JsonResource
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
            
            // Read repair_details right required
            $this->mergeWhen($loggedUser->hasPermissionTo('read repair_details'), [
                'checked' => false,
                'repair_id' => $this->repair_id,
                'company_id' => $this->company_id,
                'company' => new CompanyResource($this->company),
                'cust_name' => $this->when($loggedUser->hasPermissionTo('read customers'), $this->repair && $this->repair->equipment && $this->repair->equipment->customer ? $this->repair->equipment->customer->name : ($this->customer ? $this->customer->name : "" )),
                'repair_number' => $this->repair && $this->repair->number ? $this->repair->number :  "",
                'equipment_id' => $this->repair && $this->repair->equipment_id ? $this->repair->equipment_id : 0,
                // 'repair' => new RepairResource($this->repair),
                // 'user_id' => $this->user_id,
                // 'user' => new User($this->user),

                'invoice_pdf_generated' => $this->invoice_pdf_generated,
            
                // 'invoice_number_pref' => $this->invoice_number_pref,
                // 'invoice_number_year' => $this->invoice_number_year,
                // 'invoice_number_suff' => $this->invoice_number_suff,
                'invoice_number' => $this->invoice_number,
                // 'invoice_detailed_number' => $this->invoice_detailed_number,
                'invoice_date' => $this->invoice_date,
                'delivery_date' => $this->delivery_date,
                'customer_id' => $this->customer_id,
                'customer' => $this->when($loggedUser->hasPermissionTo('read customers'), new CustomerResource($this->customer)),
                'customer_name' => $this->when($loggedUser->hasPermissionTo('read customers'), $this->customer && $this->customer->name ? $this->customer->name : ($this->repair && $this->repair->equipment && $this->repair->equipment->customer ? $this->repair->equipment->customer->name : "") ),
                
                'offer_number' => $this->offer_number,
                'offer_date' => $this->offer_date,
                'order_date' => $this->order_date,
                'order_number' => $this->order_number,
                'client' => $this->client,

                'due_days' => $this->due_days,
                'discount_days' => $this->discount_days,
                'discount_amount' => $this->when($loggedUser->hasPermissionTo('access_prices_offer'), $this->discount_amount),
                // 'discount_amount' => $this->discount_amount,
                'due_date' => $this->due_date,
                // 'discount_date' => $this->discount_date,
                'payment_date' => $this->payment_date,
                'pdf' => [
                    'id' => $this->id,
                    'invoice_pdf_generated' => $this->invoice_pdf_generated,
                    'invoice_number' => $this->invoice_number
                ],

                $this->mergeWhen($loggedUser->hasPermissionTo('access_prices_offer'), [
                    // 'es_price' => $this->es_price,
                    // 'rr_price' => $this->rr_price,
                    // 'wh_price' => $this->wh_price,
                    // 'empl_dr_price' => $this->empl_dr_price,
                    // 'dr_price' => $this->dr_price,
                    // 'total' => $this->total,
                    // 'total_vat' => $this->total_vat,
                    'total_with_vat' => $this->total_with_vat,
                ]),

                'extra_services' => ExtraServiceResource::collection($this->extraServices)
            ]),

            'created_at' => date('Y-m-d',strtotime($this->created_at))
        ];
    }
}

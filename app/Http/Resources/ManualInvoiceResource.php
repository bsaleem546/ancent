<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;
use Illuminate\Support\Facades\Auth;

class ManualInvoiceResource extends JsonResource
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
                'user_id' => $this->user_id,
                'user' => new User($this->user),

                'invoice_number' => $this->invoice_number,
                'invoice_detailed_number' => $this->invoice_detailed_number,
                'invoice_date' => $this->invoice_date,
                'delivery_date' => $this->delivery_date,
                
                'customer_id' => $this->customer_id,
                // 'customer' => new CustomerResource($this->customer),
                'customer' => $this->when($loggedUser->hasPermissionTo('read customers'), new CustomerResource($this->customer)),

                'invoice_pdf_generated' => $this->invoice_pdf_generated,
            
                'offer_number' => $this->offer_number,
                'offer_date' => $this->offer_date,
                'order_date' => $this->order_date,
                'order_number' => $this->order_number,
                'client' => $this->client,

                'due_days' => $this->due_days,
                'discount_days' => $this->discount_days,
                'discount_amount' => $this->discount_amount,
                // 'discount_amount' => $this->discount_amount,
                'due_date' => $this->due_date,
                'discount_date' => $this->discount_date,
                'payment_date' => $this->payment_date,

                // $this->mergeWhen($loggedUser->hasAllPermissions(['access_prices_offer']), [
                    'es_price' => $this->es_price,
                    'rr_price' => $this->rr_price,
                    'wh_price' => $this->wh_price,
                    'empl_dr_price' => $this->empl_dr_price,
                    'dr_price' => $this->dr_price,
                    'total' => $this->total,
                    'total_vat' => $this->total_vat,
                    'total_with_vat' => $this->total_with_vat,
                // ]),

                'extra_services' => ExtraServiceResource::collection($this->extraServices)
            ]),

            'created_at' => date('Y-m-d',strtotime($this->created_at))
        ];
    }
}

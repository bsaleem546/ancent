<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;
use Illuminate\Support\Facades\Auth;

class CustomerResource extends JsonResource
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
            'name' => $this->name,
            'street' => $this->street,
            'postal_code' => $this->postal_code,
            'place' => $this->place,
            'country' => $this->country,
            'contact_person' => $this->contact_person,
            'phone' => $this->phone,
            'fax' => $this->fax,
            'email' => $this->email,
            'notes' => (isset($this->notes)) ? $this->notes : "",
            // Only  send the internal_notes if the requesting user has access to internal notes
            $this->mergeWhen(Auth::user()->CP('access_internal_notes'), [
                'internal_notes' => (isset($this->internal_notes)) ? $this->internal_notes : "",
            ]),
            'id_rw' => $this->id_rw,
            'vat_id' => $this->vat_id,
            'invoice_prefix' => $this->invoice_prefix,
            'accounting_area' => $this->accounting_area,
            'supplier_number' => $this->supplier_number,
            'active' => $this->active,
            $this->mergeWhen(Auth::user()->CPA(['access_prices_offer']), [
                'discount' => $this->discount,
                'customer_rates' => $this->customerRates,
                'valid_customer_rates' => $this->validCustomerRates,
            ]),
        ];
    }
}

<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;
use Illuminate\Support\Facades\Auth;

class UserResource extends JsonResource
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
            'username' => $this->username,
            'email' => $this->email,
            // Only  send the api_token if the requesting user is super admin
            // 'api_token' => $this->when(Auth::user()->hasRole('super_user'), $this->api_token),
            // 'api_token' => $this->api_token,
            'is_super_admin' => $this->is_super_admin,
            'permissions' => $this->formatPermissions(),

            // User Details section
            $this->mergeWhen(is_object($this->userDetails), [
                'first_name' => (isset($this->userDetails->first_name)) ? $this->userDetails->first_name : "",
                'last_name' => (isset($this->userDetails->last_name)) ? $this->userDetails->last_name : "",
                'phone' => (isset($this->userDetails->phone)) ? $this->userDetails->phone : "",
                'fax' => (isset($this->userDetails->fax)) ? $this->userDetails->fax : "",
                'sms' => (isset($this->userDetails->sms)) ? $this->userDetails->sms : "",
                'notes' => (isset($this->userDetails->notes)) ? $this->userDetails->notes : "",
                $this->mergeWhen(Auth::user()->CP('read customers'), [
                    'customer_id' => (isset($this->userDetails->customer_id)) ? $this->userDetails->customer_id : "",
                    'customer' => (isset($this->userDetails->customer_id) && is_object($this->userDetails->customer) && isset($this->userDetails->customer)) ? $this->userDetails->customer->name : "",
                ]),
                $this->mergeWhen(Auth::user()->CP('read operators'), [
                    'operator_id' => (isset($this->userDetails->operator_id)) ? $this->userDetails->operator_id : "",
                    'operator' => (isset($this->userDetails->operator_id) && is_object($this->userDetails->operator) && isset($this->userDetails->operator)) ? $this->userDetails->operator->name : "",
                ]),
                $this->mergeWhen(Auth::user()->CPA(['read customers', 'read operators']), [
                    'customer_operator_filter' => (isset($this->userDetails->customer_operator_filter)) ? $this->userDetails->customer_operator_filter : "",
                ]),
            ])
        ];
    }
}

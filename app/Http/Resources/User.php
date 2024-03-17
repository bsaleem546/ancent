<?php
/**
 * User rsource class used to send minimal data to the UI for UserCollections
 * DO NOT confuse with UserResource that is used to return a single User's data to the UI
 * And that contains all the user details(password excluded)
 *
 */
namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;
use Illuminate\Support\Facades\Auth;

class User extends JsonResource
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
            // Reduce the size of the data sent to the UI for Collections
            // 'api_token' => $this->when(Auth::user()->hasRole('super_user'), $this->api_token),
            // 'is_super_admin' => $this->is_super_admin,
            // 'permissions' => $this->formatPermissions(),

            // User Details section
            $this->mergeWhen(is_object($this->userDetails), [
                'first_name' => (is_object($this->userDetails) && isset($this->userDetails->first_name)) ? $this->userDetails->first_name : "",
                'last_name' => (is_object($this->userDetails) && isset($this->userDetails->last_name)) ? $this->userDetails->last_name : "",
                // Reduce the size of the data sent to the UI for Collections
                // 'phone' => (is_object($this->userDetails) && isset($this->userDetails->phone)) ? $this->userDetails->phone : "",
                // 'fax' => (is_object($this->userDetails) && isset($this->userDetails->fax)) ? $this->userDetails->fax : "",
                // 'sms' => (is_object($this->userDetails) && isset($this->userDetails->sms)) ? $this->userDetails->sms : "",
                // 'notes' => (is_object($this->userDetails) && isset($this->userDetails->notes)) ? $this->userDetails->notes : "",
                $this->mergeWhen(Auth::user()->hasPermissionTo('read customers'), [
                    'customer_id' => (isset($this->userDetails->customer_id)) ? $this->userDetails->customer_id : "",
                    'customer' => (isset($this->userDetails->customer_id) && is_object($this->userDetails->customer) && isset($this->userDetails->customer)) ? $this->userDetails->customer->name : "",
                ]),
                $this->mergeWhen(Auth::user()->hasPermissionTo('read operators'), [
                    'operator_id' => (isset($this->userDetails->operator_id)) ? $this->userDetails->operator_id : "",
                    'operator' => (isset($this->userDetails->operator_id) && is_object($this->userDetails->operator) && isset($this->userDetails->operator)) ? $this->userDetails->operator->name : "",
                ]),
                // $this->mergeWhen(Auth::user()->hasAllPermissions(['read customers', 'read operators']), [
                //     'customer_operator_filter' => (isset($this->userDetails->customer_operator_filter)) ? $this->userDetails->customer_operator_filter : "",
                // ]),
            ])
        ];
    }
}

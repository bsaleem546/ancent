<?php

namespace App\Http\Controllers;

use App\Http\Resources\CustomerResource;
use App\Models\Customer;
use App\Models\CustomerRates;
use App\Models\Repair;
use App\Traits\Filters\GeneralCustomerFilters;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Auth;

class CustomerController extends Controller
{
    private function customerValidator(array $data)
    {
        $validator = Validator::make($data, [
            'name' => ['required', 'string', 'max:100'],
            // 'street' => ['required', 'string', 'max:100'],
            // 'postal_code' => ['required', 'string', 'max:10'],
            // 'place' => ['required', 'string', 'max:100'],
            // 'country' => ['required', 'string', 'max:50'],
            // 'contact_person' => ['required', 'string'],
            // 'phone' => ['sometimes', new PhoneNumber],
            // 'fax' => ['sometimes', new PhoneNumber],
            // 'email' => ['required',  'email'],
            // 'notes' => ['sometimes', 'string'],
            // 'internal_notes' => ['sometimes', 'string'],
            // 'id_rw' => ['required', 'integer'],
            // 'vat_id' => ['required', 'integer'],
            // 'invoice_prefix' => ['required', 'string'],
            // 'accounting_area' => ['required', 'string'],
            // 'supplier_number' => ['required', 'string'],
            // 'active' => ['required', 'integer']
        ]);

        return $validator;
    }

    public function getAll(Request $request, GeneralCustomerFilters $filters)
    {
        return CustomerResource::collection(Customer::filter($filters)
            ->paginate($request->pagination["per_page"], ['*'], 'page', $request->pagination["current_page"]));
    }

    public function show($id)
    {
        return new CustomerResource(Customer::findOrFail($id));
    }

    public function update(Request $request)
    {
        $apiResponse = array(
            'success' => false
        );

        $validator = $this->customerValidator($request->all());
        if ($validator->fails()) {
            $apiResponse['errors'] = $validator->messages();
            return $apiResponse;
        }

        if (isset($request->id) && $request->id != 0) {
            $customer = Customer::findOrFail($request->id);

            $customer->name = $request->name;

            if (isset($request->street)) $customer->street = $request->street;
            if (isset($request->postal_code)) $customer->postal_code = $request->postal_code;
            if (isset($request->place)) $customer->place = $request->place;
            if (isset($request->country)) $customer->country = $request->country;
            if (isset($request->contact_person)) $customer->contact_person = $request->contact_person;
            if (isset($request->phone)) $customer->phone = $request->phone;
            if (isset($request->fax)) $customer->fax = $request->fax;
            if (isset($request->email)) $customer->email = $request->email;
            if (isset($request->notes)) $customer->notes = $request->notes;
            if (isset($request->internal_notes) && Auth::user()->CP('access_internal_notes')) $customer->internal_notes = $request->internal_notes;
//            if (isset($request->internal_notes) && Auth::user()->hasPermissionTo('access_internal_notes')) $customer->internal_notes = $request->internal_notes;
            if (isset($request->id_rw)) $customer->id_rw = $request->id_rw;
            if (isset($request->vat_id)) $customer->vat_id = $request->vat_id;
            if (isset($request->invoice_prefix)) $customer->invoice_prefix = $request->invoice_prefix;
            if (isset($request->accounting_area)) $customer->accounting_area = $request->accounting_area;
            if (isset($request->supplier_number)) $customer->supplier_number = $request->supplier_number;
            if (isset($request->discount) && Auth::user()->CPA(['access_prices_offer'])) $customer->discount = $request->discount;
//            if (isset($request->discount) && Auth::user()->hasAllPermissions(['access_prices_offer'])) $customer->discount = $request->discount;
            if (isset($request->active)) $customer->active = $request->active;

            $customer->save();

            $oldStdRate = CustomerRates::getStandardRate($request->id);
            if ($oldStdRate) {
                $newStdRate = null;
                foreach ($request->customer_rates as $newRate) {
                    if ($newRate['checked'] && ((!isset($newRate['id'])) || $newRate['id'] !== $oldStdRate->id)) {
                        $newStdRate = $newRate;
                        break;
                    }
                }

                // If we have a new standard rate update the still not invoiced repairs that have the repair date
                // within the timerange of the new rate to use this new rate
                if ($newStdRate) {
                    // Update all orders that have the old standard rate set
                    $repairsUpdated = Repair::updateAllRepairsNotInvoicedForRate($oldStdRate->id, $newStdRate, $newStdRate['valid_from'], $newStdRate['valid_to']);
                    // error_log("Repairs Updated: ". json_encode($repairsUpdated));
                }
            }

            // Handle any customer rates updates
            if (Auth::user()->CPA(['access_prices_offer'])) {
//                if (Auth::user()->hasAllPermissions(['access_prices_offer'])) {
                $ratesSync = $customer->syncRates($request->customer_rates);
                error_log("Synced rates: " . json_encode($ratesSync));
            }

            return new CustomerResource($customer);
        } else {
            $apiResponse = array(
                'success' => false
            );
            $apiResponse['errors'] = "customer id missing";

            return $apiResponse;
        }
    }

    public function store(Request $request)
    {
        $apiResponse = array(
            'success' => false
        );

        $validator = $this->customerValidator($request->all());
        if ($validator->fails()) {
            $apiResponse['errors'] = $validator->messages();
            return response()->json($apiResponse, 422);
        }

        $customer = new Customer;

        $customer->name = $request->name;

        if (isset($request->street)) $customer->street = $request->street;
        if (isset($request->postal_code)) $customer->postal_code = $request->postal_code;
        if (isset($request->place)) $customer->place = $request->place;
        if (isset($request->country)) $customer->country = $request->country;
        if (isset($request->contact_person)) $customer->contact_person = $request->contact_person;
        if (isset($request->phone)) $customer->phone = $request->phone;
        if (isset($request->fax)) $customer->fax = $request->fax;
        if (isset($request->email)) $customer->email = $request->email;
        if (isset($request->notes)) $customer->notes = $request->notes;
        if (isset($request->internal_notes) && Auth::user()->CP('access_internal_notes')) $customer->internal_notes = $request->internal_notes;
//        if (isset($request->internal_notes) && Auth::user()->hasPermissionTo('access_internal_notes')) $customer->internal_notes = $request->internal_notes;
        if (isset($request->id_rw)) $customer->id_rw = $request->id_rw;
        if (isset($request->vat_id)) $customer->vat_id = $request->vat_id;
        if (isset($request->invoice_prefix)) $customer->invoice_prefix = $request->invoice_prefix;
        if (isset($request->accounting_area)) $customer->accounting_area = $request->accounting_area;
        if (isset($request->supplier_number)) $customer->supplier_number = $request->supplier_number;
        if (isset($request->discount) && Auth::user()->CPA(['access_prices_offer'])) $customer->discount = $request->discount;
//        if (isset($request->discount) && Auth::user()->hasAllPermissions(['access_prices_offer'])) $customer->discount = $request->discount;
        if (isset($request->active)) $customer->active = $request->active;

        $customer->save();

        error_log("Rates: " . json_encode($request->customer_rates));

//        if (Auth::user()->hasAllPermissions(['access_prices_offer']) &&
        if (Auth::user()->CPA(['access_prices_offer']) &&
            (!is_null($request->customer_rates)) && is_array($request->customer_rates) && count($request->customer_rates) > 0) {
            $customer->customerRates()->createMany($request->customer_rates);
        }

        return new CustomerResource($customer);
    }

    public function destroy($id)
    {
        $customer = Customer::findOrFail($id);

        $customer->customerRates()->delete();

        $customer->delete();
        return new CustomerResource($customer);
    }
}

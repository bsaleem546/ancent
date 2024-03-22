<?php

namespace App\Http\Controllers;

use App\Http\Resources\RepairCollectionResource;
use App\Http\Resources\RepairMinCollectionResource;
use App\Http\Resources\RepairResource;
use App\Models\CustomerRates;
use App\Models\Equipment;
use App\Models\Invoice;
use App\Models\Location;
use App\Models\Repair;
use App\Models\Vat;
use App\Traits\Filters\RepairFilters;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class RepairController extends Controller
{
    public function showP(Request $request): RepairResource
    {
        return new RepairResource(Repair::findOrFail($request->id));
    }

    public function showMultiple(Request $request): RepairResource
    {
        $query = Repair::query();
        if (isset($request->repairs) && $request->repairs != "") {
            $query->whereIn('id', $request->repairs);
        }

        $repairs = $query->get();
        error_log("Repairs: " . json_encode(RepairResource::collection($repairs)));

        return RepairResource::collection($repairs);
    }

    public function getAll(Request $request, RepairFilters $filters)
    {
        $query = Repair::query();
        if (isset($request->equipment_id) && $request->equipment_id != "") {
            $query->where('equipment_id', $request->equipment_id);
        }

        $repairs = $query->filter($filters)->paginate($request->pagination["per_page"], ['*'],
            'page', $request->pagination["current_page"]);

        return new RepairCollectionResource($repairs);
    }

    public function getAllMinData(Request $request, RepairFilters $filters)
    {
        $query = Repair::query();
        if (isset($request->equipment_id) && $request->equipment_id != "") {
            $query->where('equipment_id', $request->equipment_id);
        }
        $repairs = $query->select('id', 'equipment_id', 'number');
        $repairs = $query->paginate($request->pagination["per_page"], ['*'],
            'page', $request->pagination["current_page"]);

        return new RepairMinCollectionResource($repairs);
    }

    public function show($id)
    {
        return new RepairResource(Repair::findOrFail($id));
    }

    public function getRepairsByIds(Request $request)
    {
        $query = Repair::query();
        if (isset($request->repairs) && $request->repairs != "") {
            $query->whereIn('id', $request->repairs);
        }

        $repairs = $query->get();
        error_log("Repairs: " . json_encode(RepairResource::collection($repairs)));

        return RepairResource::collection($repairs);
    }

    public function update(Request $request)
    {
        $apiResponse = array(
            'success' => false
        );

        if (isset($request->id) && $request->id != 0) {
            $apiData = array(
                'success' => false,
                'message' => 'forbidden_no_permission',
            );

            $repair = Repair::findOrFail($request->id);
            $loggedUser = Auth::user();
            // This ones should not be changed during updates
            // if(isset($request->equipment_id)) $repair->equipment_id = $request->equipment_id;
            // if(isset($request->user_id)) $repair->user_id = $request->user_id;
            // if(isset($request->number)) $repair->number = $request->number;

            // if($loggedUser->hasPermissionTo('write repairs'))
            // if($loggedUser->hasAnyPermission(['access_internal_notes', 'read repairs']))
            // if($loggedUser->hasAllPermissions(['access_internal_notes', 'read repairs']))

            // Do not make any updates if the invoice has been generated already
            if ($repair->invoice && $repair->invoice['invoice_pdf_generated']) {
                error_log("Invoice pdf generated. No updates allowed except internal notes and payment date inside invoice.");
                if ($loggedUser->hasAnyPermission(['create repair_details', 'write repair_details', 'write repairs'])) {
                    if (isset($request->invoice)) {
                        $invoice = $repair->invoice;
                        /* if(isset($request->invoice['payment_date'])) */
                        $invoice->payment_date = isset($request->invoice['payment_date']) ? $request->invoice['payment_date'] : null;
                        if (isset($request->invoice['payment_date'])) {
                            $invoice->payment_date = $request->invoice['payment_date'];
                            $repair->status = 'invoice_paid';
                        } else {
                            $invoice->payment_date = null;
                            $repair->status = 'invoice_generated';
                        }
                        $repair->invoice()->save($invoice);
                    }

                    if (isset($request->internal_notes) && $loggedUser->hasPermissionTo('access_internal_notes')) $repair->internal_notes = $request->internal_notes;
                    $repair->save();
                }

                return new RepairResource($repair);
            }

            // REPAIRS
            if ($loggedUser->hasPermissionTo('write repairs')) {
                if (isset($request->company_id)) $repair->company_id = $request->company_id; // This is one of the Ansent family companies
                if (isset($request->location_id)) $repair->location_id = $request->location_id;
                // Save the change of the customer_id so that we can also change the invoice customer_id
                $repairInitialCustomerId = 0;
                if (isset($request->customer_id)) {
                    if ($repair->customer_id != $request->customer_id) $repairInitialCustomerId = $repair->customer_id;
                    $repair->customer_id = $request->customer_id;
                }
                if (isset($request->operator_id)) $repair->operator_id = $request->operator_id;
                if (isset($request->status)) {
                    $repair->status = $request->status;
                    if ($request->status == 'offer_needed') {
                        $repair->had_offer_needed = 1;
                    }
                }
                if (isset($request->work_description)) $repair->work_description = $request->work_description;
                if (isset($request->internal_notes) && $loggedUser->hasPermissionTo('access_internal_notes')) $repair->internal_notes = $request->internal_notes;
                if (isset($request->offer_needed)) $repair->offer_needed = ($request->offer_needed ? 1 : 0);
            }

            // PROTOKOLL (Repair details) create repair_details | write repair_details
            if (($repair->repair_details_added && $loggedUser->hasPermissionTo('write repair_details')) || (!$repair->repair_details_added && $loggedUser->hasPermissionTo('create repair_details'))) {
                if (isset($request->repair_details_added) && !$repair->repair_details_added) $repair->repair_details_added = $request->repair_details_added;
                if ((isset($request->repair_date) && !$repair->repair_date) || (isset($request->repair_date) && $repair->repair_date && $repair->repair_date != $request->repair_date)) {
                    $repair->rough_schedule_start = $request->repair_date;
                    $repair->rough_schedule_end = $request->repair_date;
                    $repair->exact_schedule_start = null;
                    $repair->exact_schedule_end = null;
                }
                /* if(isset($request->repair_date)) */
                $repair->repair_date = isset($request->repair_date) ? $request->repair_date : null;
                if (isset($request->reviewed)) $repair->reviewed = ($request->reviewed ? 1 : 0);
                if (isset($request->repair_blocked)) $repair->repair_blocked = ($request->repair_blocked ? 1 : 0);
                if (isset($request->hours_of_operations)) $repair->hours_of_operations = floatval($request->hours_of_operations);

                // If the rate id was changed directly make the update, otherwise
                if (isset($request->rate_id) && $request->rate_id != $repair->rate_id) {
                    $repair->rate_id = $request->rate_id;
                } else { // check if the repair customer id has changed and update the rate id too
                    // If the repair customer id changed also change the rate id of the repair to the standard rate for that customer id
                    if ($repairInitialCustomerId != 0) {
                        $stdRate = CustomerRates::getStandardRate($repair->customer_id);
                        $stdRateId = 0;
                        if ($stdRate) $stdRateId = $stdRate['id'];
                        $repair->rate_id = $stdRateId;
                    }
                }

                if (isset($request->invoicing_needed)) {
                    $repair->invoicing_needed = ($request->invoicing_needed ? 1 : 0);
                } else {
                    $repair->invoicing_needed = null;
                }
                if (isset($request->active_travel_cost)) $repair->active_travel_cost = ($request->active_travel_cost ? 1 : 0);
                if (isset($request->travel_costs) && $loggedUser->hasPermissionTo('access_prices_offer')) $repair->travel_costs = floatval($request->travel_costs);
                if (isset($request->travel_cost_factor) && $loggedUser->hasPermissionTo('access_prices_offer')) $repair->travel_cost_factor = floatval($request->travel_cost_factor);
                if (isset($request->active_km)) $repair->active_km = ($request->active_km ? 1 : 0);
                if (isset($request->km_costs) && $loggedUser->hasPermissionTo('access_prices_offer')) $repair->km_costs = floatval($request->km_costs);
                if (isset($request->active_per_km)) $repair->active_per_km = ($request->active_per_km ? 1 : 0);
                if (isset($request->km)) $repair->km = floatval($request->km);
                if (isset($request->costs_per_km) && $loggedUser->hasPermissionTo('access_prices_offer')) $repair->costs_per_km = floatval($request->costs_per_km);
                if (isset($request->active)) $repair->active = ($request->active ? 1 : 0);

                // Only Update the working hours and the repair replacements if the user has create or write repair details rights
                if (Auth::user()->hasAnyPermission(['create repair_details', 'write repair_details'])) {
                    $invoice = $repair->invoice;
                    if (!$invoice) {
                        error_log("Invoice not found. Create new entry.");
                        $invoice = new Invoice();
                        // Add default values for the invoice if it did not exist(DB import issue)
                        $invoice->customer_id = $repair->customer_id;

                        $oldStdRate = CustomerRates::getStandardRate($repair->customer_id);
                        if ($oldStdRate) {
                            $invoice->due_days = $oldStdRate->due_days;
                            $invoice->discount_days = $oldStdRate->discount_days;
                            $invoice->discount_amount = $oldStdRate->discount_amount;
                        } else {
                            $invoice->due_days = 14;
                            $invoice->discount_days = 0;
                            $invoice->discount_amount = 0;
                        }
                        $repair->invoice()->save($invoice);
                        $repair->save();
                        $repair = Repair::findOrFail($request->id);
                    }

                    $invoice = $repair->invoice;

                    // Sync any time tracking changes
                    if (isset($request->time_tracking)) {
                        $timeTrackingSync = $repair->syncOneToMany($request->time_tracking, $repair->timeTracking());
                        error_log("Synced time tracking: " . json_encode($timeTrackingSync));

                        $totalDriveKm = 0;
                        foreach ($repair->timeTracking as $i => $timeTr) {
                            // Drive calculation
                            $driveToKm = isset($timeTr['drive_to_km']) ? $timeTr['drive_to_km'] : 0;
                            $driveFromKm = isset($timeTr['drive_from_km']) ? $timeTr['drive_from_km'] : 0;
                            $driveKm = $driveToKm + $driveFromKm;
                            error_log("Drive KM per employee: " . $driveKm);
                            $totalDriveKm += $driveKm;
                        }
                        error_log("Total drive KM: " . $totalDriveKm);
                    }

                    // Sync any customer invoicing changes
                    if (isset($request->customer_invoicing)) {
                        // $customerInvoicingSync = $repair->syncOneToMany($request->customer_invoicing, $repair->customerInvoicing());
                        // error_log("Synced customer invoicing: ". json_encode($customerInvoicingSync));

                        $totalWHPrice = 0;
                        $emplTotalDRPrice = 0;
                        // $stdRate = CustomerRates::getStandardRate($invoice->customer_id);
                        $stdRate = $repair->rate;
                        $customerInvoicing = $request->customer_invoicing;

                        foreach ($request->customer_invoicing as $i => $wHour) {
                            if (isset($wHour['driving_time']) && $wHour['driving_time']) {
                                if (isset($wHour['internal']) && $wHour['internal']) {
                                    // Value is 0 for the internal driving times
                                    // $drPrice   = isset($stdRate['travel_costs']) ? $stdRate['travel_costs'] : 0;
                                    $customerInvoicing[$i]["unit_cost"] = 0;
                                    $customerInvoicing[$i]["total_cost"] = 0;
                                    $emplTotalDRPrice += 0;
                                } else {
                                    // DR calculation
                                    $driveH = isset($wHour['work_h']) ? $wHour['work_h'] : 0;
                                    $driveM = isset($wHour['work_min']) ? $wHour['work_min'] : 0;
                                    $driveTime = $driveH + ($driveM / 60);
                                    $drPrice = isset($stdRate['travel_costs']) ? $stdRate['travel_costs'] : 0;
                                    $customerInvoicing[$i]["unit_cost"] = $drPrice;
                                    $roundedDRPrice = round(($driveTime * $drPrice), 2);
                                    $customerInvoicing[$i]["total_cost"] = $roundedDRPrice;
                                    $emplTotalDRPrice += $roundedDRPrice;

                                    error_log("Travel H: " . $driveH);
                                    error_log("Travel M: " . $driveM);
                                    error_log("Travel time: " . $driveTime);
                                    error_log("Travel price: " . $roundedDRPrice);
                                    error_log("Total WH Travel price: " . $emplTotalDRPrice);
                                }
                            } else {
                                if (isset($wHour['internal']) && $wHour['internal']) {
                                    // Value is 0 for the internal driving times
                                    // $whPrice  = isset($stdRate['work_costs']) ? $stdRate['work_costs'] : 0;
                                    $customerInvoicing[$i]["unit_cost"] = 0;
                                    $customerInvoicing[$i]["total_cost"] = 0;
                                    $emplTotalDRPrice += 0;
                                } else {
                                    // WH calculation
                                    $workH = isset($wHour['work_h']) ? $wHour['work_h'] : 0;
                                    $workM = isset($wHour['work_min']) ? $wHour['work_min'] : 0;
                                    $workTime = $workH + ($workM / 60);
                                    $whPrice = isset($stdRate['work_costs']) ? $stdRate['work_costs'] : 0;
                                    $customerInvoicing[$i]["unit_cost"] = $whPrice;
                                    $roundedWHPrice = round(($workTime * $whPrice), 2);
                                    $customerInvoicing[$i]["total_cost"] = $roundedWHPrice;
                                    $totalWHPrice += $roundedWHPrice;

                                    error_log("Work H: " . $workH);
                                    error_log("Work M: " . $workM);
                                    error_log("Work time: " . $workTime);
                                    error_log("Work price: " . $roundedWHPrice);
                                    error_log("Total WH price: " . $totalWHPrice);
                                }
                            }
                        }
                        error_log("Customer invoicing: " . json_encode($customerInvoicing));

                        $customerInvoicingSync = $repair->syncOneToMany($customerInvoicing, $repair->customerInvoicing());
                        error_log("Synced customer invoicing: " . json_encode($customerInvoicingSync));

                        $invoice->wh_price = $totalWHPrice;
                        $invoice->empl_dr_price = $emplTotalDRPrice;

                        if ($repair->active_travel_cost) {
                            $factor = isset($repair->travel_cost_factor) ? $repair->travel_cost_factor : 1;
                            error_log("Factor: " . $factor);
                            $travelCost = isset($repair->travel_costs) ? $repair->travel_costs : 0;
                            error_log("Travel cost: " . $travelCost);
                            $invoice->dr_price = ($factor * $travelCost) + $invoice->empl_dr_price;
                            error_log("Anfahrtpauschale: " . $invoice->dr_price);
                        } else if ($repair->active_km) {
                            $kmCost = isset($repair->km_costs) ? $repair->km_costs : 0;
                            $invoice->dr_price = $kmCost + $invoice->empl_dr_price;
                            error_log("Kilometerpauschale: " . $invoice->dr_price);
                        } else if ($repair->active_per_km) {
                            $perKmCost = isset($repair->costs_per_km) ? $repair->costs_per_km : 0;
                            $km = isset($repair->km) ? $repair->km : 0;
                            $trCost = round(($perKmCost * $km), 2);
                            $invoice->dr_price = $trCost + $invoice->empl_dr_price;
                            error_log("Per Km price: " . $invoice->dr_price);
                        } else {
                            $invoice->dr_price = $invoice->empl_dr_price;
                            error_log("No driving costs: " . $invoice->dr_price);
                        }
                    }

                    // Sync any repair replacements changes
                    if (isset($request->repair_replacements)) {
                        $repairReplacementsSync = $repair->syncOneToMany($request->repair_replacements, $repair->repairReplacements());
                        error_log("Synced repair replacements: " . json_encode($repairReplacementsSync));

                        $totalRRPrice = 0;
                        foreach ($repair->repairReplacements as $i => $rReplacement) {
                            $replacementPrice = isset($rReplacement['price']) ? $rReplacement['price'] : 0;
                            $replacementCount = isset($rReplacement['count']) ? $rReplacement['count'] : 0;
                            $replacementDiscount = isset($rReplacement['discount']) ? $rReplacement['discount'] : 0;
                            $rReplacementPrice = ($replacementPrice * $replacementCount) - ($replacementPrice * $replacementCount * ($replacementDiscount / 100));
                            $roundedRRPrice = round($rReplacementPrice, 2);
                            // $request->repair_replacements[$i]['rr_price'] = $roundedRRPrice;
                            $totalRRPrice += $roundedRRPrice;
                        }

                        $invoice->rr_price = $totalRRPrice;
                    }
                } else {
                    if (!Auth::user()->hasAnyPermission(['create repairs', 'write repairs'])) {
                        $apiData['write repair_details'][] = "time_tracking";
                        $apiData['write repair_details'][] = "customer_invoicing";
                        $apiData['write repair_details'][] = "repair_replacements";
                        return response()->json($apiData, 403);
                    }
                }

                if (isset($request->invoice)) {
                    error_log("We have request.invoice. Update with invoice data from request.");
                    if (isset($request->invoice['invoice_number_pref'])) $invoice->invoice_number_pref = $request->invoice['invoice_number_pref'];
                    if (isset($request->invoice['invoice_number_year'])) $invoice->invoice_number_year = $request->invoice['invoice_number_year'];
                    if (isset($request->invoice['invoice_number_suff'])) $invoice->invoice_number_suff = $request->invoice['invoice_number_suff'];
                    if (isset($request->invoice['invoice_number'])) $invoice->invoice_number = $request->invoice['invoice_number'];
                    if (isset($request->invoice['invoice_detailed_number'])) $invoice->invoice_detailed_number = $request->invoice['invoice_detailed_number'];
                    /* if(isset($request->invoice['invoice_date'])) */
                    $invoice->invoice_date = isset($request->invoice['invoice_date']) ? $request->invoice['invoice_date'] : null;
                    // Keep the repair date in sync with the invoice delivery date
                    if (isset($repair->repair_date)) $invoice->delivery_date = $repair->repair_date;

                    // If the invoice customer id was changed directly make the update, otherwise
                    if (isset($request->invoice['customer_id']) && $request->invoice['customer_id'] != $invoice->customer_id) {
                        $invoice->customer_id = $request->invoice['customer_id'];
                    } else { // check if the repair customer id has changed and update the invoice customer id too
                        // If the repair customer id changed and the previous customer id is equal to the invoice customer id
                        // also change the customer id of the invoice to the same customer id of the repair
                        if ($repairInitialCustomerId != 0 && $invoice->customer_id == $repairInitialCustomerId) {
                            $invoice->customer_id = $request->customer_id;
                        } else {
                            // Else the invoice customer id was changed by the user in previous updates so do not update - DO nothing
                        }
                    }

                    if (isset($request->invoice['company_id'])) $invoice->company_id = $request->invoice['company_id']; // This is one of the Ansent family companies
                    if (isset($request->invoice['offer_number'])) $invoice->offer_number = $request->invoice['offer_number'];
                    /* if(isset($request->invoice['offer_date'])) */
                    $invoice->offer_date = isset($request->invoice['offer_date']) ? $request->invoice['offer_date'] : null;
                    /* if(isset($request->invoice['order_date'])) */
                    $invoice->order_date = isset($request->invoice['order_date']) ? $request->invoice['order_date'] : null;
                    if (isset($request->invoice['order_number'])) $invoice->order_number = $request->invoice['order_number'];
                    if (isset($request->invoice['client'])) $invoice->client = $request->invoice['client'];

                    // If the due days was changed directly make the update, otherwise
                    if (isset($request->invoice['due_days']) && $request->invoice['due_days'] != $invoice->due_days) {
                        $invoice->due_days = $request->invoice['due_days'];
                    } else { // check if the repair customer id has changed and update the due days too
                        // If the repair customer id changed also change the due days of the invoice to the standard rate for that customer id
                        if ($repairInitialCustomerId != 0) {
                            $stdRate = CustomerRates::getStandardRate($repair->customer_id);
                            $stdRateDueDays = 14;
                            if ($stdRate) $stdRateDueDays = $stdRate['due_days'];
                            $invoice->due_days = $stdRateDueDays;
                        }
                    }
                    // If the discount days was changed directly make the update, otherwise
                    if (isset($request->invoice['discount_days']) && $request->invoice['discount_days'] != $invoice->discount_days) {
                        $invoice->discount_days = $request->invoice['discount_days'];
                    } else { // check if the repair customer id has changed and update the discount days too
                        // If the repair customer id changed also change the discount days of the invoice to the standard rate for that customer id
                        if ($repairInitialCustomerId != 0) {
                            $stdRate = CustomerRates::getStandardRate($repair->customer_id);
                            $stdRateDiscountDays = 0;
                            if ($stdRate) $stdRateDiscountDays = $stdRate['discount_days'];
                            $invoice->discount_days = $stdRateDiscountDays;
                        }
                    }
                    // If the discount amount was changed directly make the update, otherwise
                    if (isset($request->invoice['discount_amount']) && $request->invoice['discount_amount'] != $invoice->discount_amount) {
                        $invoice->discount_amount = $request->invoice['discount_amount'];
                    } else { // check if the repair customer id has changed and update the discount amount too
                        // If the repair customer id changed also change the discount amount of the invoice to the standard rate for that customer id
                        if ($repairInitialCustomerId != 0) {
                            $stdRate = CustomerRates::getStandardRate($repair->customer_id);
                            $stdRateDiscountAmount = 0;
                            if ($stdRate) $stdRateDiscountAmount = $stdRate['discount_amount'];
                            $invoice->discount_amount = $stdRateDiscountAmount;
                        }
                    }
                    // if(isset($request->invoice['due_days'])) $invoice->due_days = $request->invoice['due_days'];
                    // if(isset($request->invoice['discount_days'])) $invoice->discount_days = $request->invoice['discount_days'];
                    // if(isset($request->invoice['discount_amount'])) $invoice->discount_amount = $request->invoice['discount_amount'];

                    if ($invoice->invoice_date) {
                        $invoice->due_date = Carbon::createFromFormat('Y-m-d', $invoice->invoice_date)->addDays($invoice->due_days);
                        $invoice->discount_date = Carbon::createFromFormat('Y-m-d', $invoice->invoice_date)->addDays($invoice->discount_days);
                    }

                    if (isset($request->invoice['extra_services'])) {
                        $extraServicesSync = $invoice->syncOneToMany($request->invoice['extra_services'], $invoice->extraServices());
                        error_log("Synced extra services: " . json_encode($extraServicesSync));

                        $totalESPrice = 0;
                        foreach ($invoice->extraServices as $i => $eService) {
                            $partPrice = isset($eService['price']) ? $eService['price'] : 0;
                            $partCount = isset($eService['count']) ? $eService['count'] : 0;
                            $partDiscount = isset($eService['discount']) ? $eService['discount'] : 0;
                            $extraServicePrice = ($partPrice * $partCount) - ($partPrice * $partCount * ($partDiscount / 100));
                            $roundedESPrice = round($extraServicePrice, 2);
                            // $request->invoice['extra_services'][$i]['es_price'] = $roundedESPrice;
                            $totalESPrice += $roundedESPrice;
                        }

                        $invoice->es_price = $totalESPrice;
                    }
                }

                error_log("ES Price: " . $invoice->es_price);
                error_log("RR Price: " . $invoice->rr_price);
                error_log("WH Price: " . $invoice->wh_price);
                error_log("DR Price: " . $invoice->dr_price);

                $invoice->total = $invoice->es_price + $invoice->rr_price + $invoice->wh_price + $invoice->dr_price;
                error_log("TOTAL Price: " . $invoice->total);
                $vat = Vat::getVATForDate($repair->repair_date ? $repair->repair_date : Carbon::now());
                error_log("VAT: " . $vat);
                $invoice->vat = $vat;
                $invoice->total_vat = $invoice->total * $vat;
                error_log("TOTAL VAT: " . $invoice->total_vat);
                $invoice->total_with_vat = $invoice->total + $invoice->total_vat;
                error_log("TOTAL price with VAT: " . $invoice->total_with_vat);

                $repair->invoice()->save($invoice);
            }

            if (count($changedFields = $repair->getDirty())) {
                error_log("Updated attributes: " . json_encode($changedFields));

                $permissionsBreached = false;
                foreach ($changedFields as $changedField => $newValue) {
                    error_log("Checking updated attribute: " . $changedField);
                    if (in_array($changedField, $this->repairFields) && !Auth::user()->hasPermissionTo('write repairs')) {
                        error_log("Checking updated repair attribute: " . $changedField);
                        $apiData['write repairs'][] = $changedField;
                        $permissionsBreached = true;
                    }

                    // We need a way to determine if this is a newly created "protokoll" or an update to an already created one
                    // Ask Stan
                    if (in_array($changedField, $this->repairDetailsFields) && !Auth::user()->hasAnyPermission(['create repair_details', 'write repair_details'])) {
                        error_log("Checking updated repair details attribute: " . $changedField);
                        $apiData['write repair_details'][] = $changedField;
                        $permissionsBreached = true;
                    }
                }

                if ($permissionsBreached) {
                    return response()->json($apiData, 403);
                }
            }

            $repair->save();

            return new RepairResource($repair);
        } else {
            $apiResponse = array(
                'success' => false
            );
            $apiResponse['errors'] = "repair id missing";

            return $apiResponse;
        }
    }

    public function generateInvoice(Request $request)
    {
        $apiResponse = array(
            'success' => false
        );

        if (isset($request->id) && $request->id != 0) {
            $apiData = array(
                'success' => false,
                'message' => 'forbidden_no_permission',
            );

            $repair = Repair::findOrFail($request->id);
            $loggedUser = Auth::user();
            // Invoice (Repair details) create repair_details | write repair_details
            if (($repair->repair_details_added && $loggedUser->hasPermissionTo('write repair_details')) || (!$repair->repair_details_added && $loggedUser->hasPermissionTo('create repair_details'))) {
                $repair->status = 'invoice_generated';

                // If the repair date was not set already then set it as today when the invoice pdf is generated
                if (!$repair->repair_date) $repair->repair_date = Carbon::now()->format('Y-m-d');

                if (isset($request->invoice)) {
                    error_log("generateInvoice - Invoice set in request. Update/Create invoice.");
                    $invoice = $repair->invoice;
                    if (!$invoice) {
                        error_log("Invoice not found. Create new entry.");
                        $invoice = new Invoice();
                        // Add default values for the invoice if it did not exist(DB import issue)
                        $invoice->customer_id = $repair->customer_id;

                        $oldStdRate = CustomerRates::getStandardRate($repair->customer_id);
                        if ($oldStdRate) {
                            $invoice->due_days = $oldStdRate->due_days;
                            $invoice->discount_days = $oldStdRate->discount_days;
                            $invoice->discount_amount = $oldStdRate->discount_amount;
                        } else {
                            $invoice->due_days = 14;
                            $invoice->discount_days = 0;
                            $invoice->discount_amount = 0;
                        }
                        $repair->invoice()->save($invoice);
                        $repair->save();
                        $repair = Repair::findOrFail($request->id);
                    }

                    $invoice = $repair->invoice;

                    if (isset($request->invoice_pdf_generated)) $invoice->invoice_pdf_generated = $request->invoice_pdf_generated;
                    $invoice->generation_date = Carbon::now();
                    $invoice->user_id = $loggedUser->id;

                    $invoice->invoice_date = $request->invoice['invoice_date'];
                    $invoice->invoice_number_pref = $request->invoice['invoice_number_pref'];
                    $invoice->invoice_number_year = $request->invoice['invoice_number_year'];

                    // Generate the number of the invoice if not received in the request
                    // RA<IDRW>-JJJJ-xxxxx
                    // Example: RA04-2020-00001
                    if (isset($request->invoice['invoice_number']) && $request->invoice['invoice_number'] != '') {
                        $invoice->invoice_number_suff = $request->invoice['invoice_number_suff'];
                        $invoice->invoice_number = $request->invoice['invoice_number'];
                    } else {
                        $lastInvoiceNumber = Invoice::getLastInvoiceNumberByYear($request->invoice['invoice_number_year']) + 1;
                        $invoice->invoice_number_suff = $lastInvoiceNumber;
                        $invoice->invoice_number = $invoice->invoice_number_pref . '-' . $invoice->invoice_number_year . '-' . str_pad($invoice->invoice_number_suff, 4, '0', STR_PAD_LEFT);
                    }

                    if ($invoice->invoice_date) {
                        $invoice->due_date = Carbon::createFromFormat('Y-m-d', $invoice->invoice_date)->addDays($invoice->due_days);
                        $invoice->discount_date = Carbon::createFromFormat('Y-m-d', $invoice->invoice_date)->addDays($invoice->discount_days);
                    }

                    $invoice->invoice_detailed_number = $invoice->invoice_detailed_number && $invoice->invoice_detailed_number != '' ? $invoice->invoice_detailed_number : 'R-' . str_replace('.', '', $repair->number) . '-' . ($repair && $repair->repair_date ? Carbon::createFromFormat('Y-m-d', $repair->repair_date)->format('Y-m') : Carbon::now()->format('Y-m'));

                    // The next ones might be unnecessary, but not sure if they expect save on generate invoice button
                    // if(isset($request->invoice['invoice_detailed_number'])) $invoice->invoice_detailed_number = $request->invoice['invoice_detailed_number'];
                    // if(isset($request->invoice['invoice_date'])) $invoice->invoice_date = $request->invoice['invoice_date'];
                    // if(isset($request->invoice['delivery_date'])) $invoice->delivery_date = $request->invoice['delivery_date'];
                    // if(isset($request->invoice['customer_id'])) $invoice->customer_id = $request->invoice['customer_id'];

                    // if(isset($request->invoice['offer_number'])) $invoice->offer_number = $request->invoice['offer_number'];
                    // if(isset($request->invoice['offer_date'])) $invoice->offer_date = $request->invoice['offer_date'];
                    // if(isset($request->invoice['order_date'])) $invoice->order_date = $request->invoice['order_date'];
                    // if(isset($request->invoice['order_number'])) $invoice->order_number = $request->invoice['order_number'];
                    // if(isset($request->invoice['client'])) $invoice->client = $request->invoice['client'];

                    // if(isset($request->invoice['due_days'])) $invoice->due_days = $request->invoice['due_days'];
                    // if(isset($request->invoice['discount_days'])) $invoice->discount_days = $request->invoice['discount_days'];
                    // if(isset($request->invoice['discount_amount'])) $invoice->discount_amount = $request->invoice['discount_amount'];

                    // If the delivery date was not already set then set it as today when the invoice pdf is generated
                    if (!$invoice->delivery_date) $invoice->delivery_date = Carbon::now();

                    $invoice->setTotalWithVatForDate($repair->repair_date ? $repair->repair_date : Carbon::now());

                    $repair->invoice()->save($invoice);

                    // if(isset($request->invoice['extra_services'])) {
                    //     $extraServicesSync = $invoice->syncOneToMany($request->invoice['extra_services'], $invoice->extraServices());
                    //     error_log("Synced extra services: ". json_encode($extraServicesSync));
                    // }
                } else {
                    $apiResponse = array(
                        'success' => false
                    );
                    $apiResponse['errors'] = "invoice object missing";

                    return $apiResponse;
                }
            }

            $repair->save();

            return new RepairResource($repair);
        } else {
            $apiResponse = array(
                'success' => false
            );
            $apiResponse['errors'] = "repair id missing";

            return $apiResponse;
        }
    }

    public function generateOffer(Request $request)
    {
        $apiResponse = array(
            'success' => false
        );

        if (isset($request->id) && $request->id != 0) {
            $apiData = array(
                'success' => false,
                'message' => 'forbidden_no_permission',
            );

            $repair = Repair::findOrFail($request->id);
            $loggedUser = Auth::user();
            // Invoice (Repair details) create repair_details | write repair_details
            if (($repair->repair_details_added && $loggedUser->hasPermissionTo('write repair_details')) || (!$repair->repair_details_added && $loggedUser->hasPermissionTo('create repair_details'))) {
                if (isset($request->invoice)) {
                    error_log("generateOffer - Invoice set in request. Update/Create invoice.");
                    $invoice = $repair->invoice;
                    if (!$invoice) {
                        error_log("Invoice not found. Create new entry.");
                        $invoice = new Invoice();
                        // Add default values for the invoice if it did not exist(DB import issue)
                        $invoice->customer_id = $repair->customer_id;

                        $oldStdRate = CustomerRates::getStandardRate($repair->customer_id);
                        if ($oldStdRate) {
                            $invoice->due_days = $oldStdRate->due_days;
                            $invoice->discount_days = $oldStdRate->discount_days;
                            $invoice->discount_amount = $oldStdRate->discount_amount;
                        } else {
                            $invoice->due_days = 14;
                            $invoice->discount_days = 0;
                            $invoice->discount_amount = 0;
                        }
                        $repair->invoice()->save($invoice);
                        $repair->save();
                        $repair = Repair::findOrFail($request->id);
                    }

                    $invoice = $repair->invoice;
                    $invoice->setTotalWithVatForDate($invoice->offer_date ? $invoice->offer_date : Carbon::now());

                    // Do not recreate the offer number
                    // $invoice->offer_number = ($invoice->offer_number && $invoice->offer_number != '') ? $invoice->offer_number : $request->invoice['offer_number'];
                    // Recreate the offer with new number
                    $invoice->offer_number = $request->invoice['offer_number'];

                    $repair->invoice()->save($invoice);
                } else {
                    $apiResponse = array(
                        'success' => false
                    );
                    $apiResponse['errors'] = "invoice object missing";

                    return $apiResponse;
                }
            }

            return new RepairResource($repair);
        } else {
            $apiResponse = array(
                'success' => false
            );
            $apiResponse['errors'] = "repair id missing";

            return $apiResponse;
        }
    }

    public function store(Request $request)
    {
        $collection = \Illuminate\Database\Eloquent\Collection::make();
        $apiResponse = array(
            'success' => false
        );

        // Get logged-in user
        $user = Auth::user();

        // Generate the number of the repair
        // Start with 60.000, increment by +1,
        // Show a dot as thousand separator - removed the dot separator in AN-515
        $lastRepairNumber = ceil(str_replace('.', '', Repair::getLastRepairNumber()));

        foreach ($request->repairs as $requestRepair) {
            // $newRepairNumber = number_format(++$lastRepairNumber, 0, ',', '.');
            // Request AN-515: remove the dot thousand separator if any
            $newRepairNumber = number_format(++$lastRepairNumber, 0, ',', '');

            $repair = new Repair;
            if (isset($requestRepair['equipment_id'])) $repair->equipment_id = $requestRepair['equipment_id'];
            if (isset($requestRepair['company_id'])) $repair->company_id = $requestRepair['company_id']; // This is one of the Ansent family companies
            if (isset($requestRepair['location_id'])) $repair->location_id = $requestRepair['location_id'];
            if (isset($requestRepair['customer_id'])) $repair->customer_id = $requestRepair['customer_id'];
            if (isset($requestRepair['operator_id'])) $repair->operator_id = $requestRepair['operator_id'];
            $repair->user_id = $user->id;
            $repair->number = $newRepairNumber;
            if (isset($requestRepair['status'])) $repair->status = $requestRepair['status'];
            if (isset($requestRepair['work_description'])) $repair->work_description = $requestRepair['work_description'];
            if (isset($requestRepair['internal_notes'])) $repair->internal_notes = $requestRepair['internal_notes'];
            if (isset($requestRepair['offer_needed'])) $repair->offer_needed = ($requestRepair['offer_needed'] ? 1 : 0);
            if (isset($requestRepair['active'])) $repair->active = ($requestRepair['active'] ? 1 : 0);
            if (isset($requestRepair['rate_id'])) $repair->rate_id = $requestRepair['rate_id'];

            // Get the equipment in case there are no right for location, customer or operator
            $equipment = Equipment::findOrFail($repair->equipment_id);

            // If we did not receive the operator_id (no operator rights) get the operator_id from the equipment
            if (!$repair->operator_id) {
                $repair->operator_id = $equipment->operator_id;
            }

            // Add the default travel costs
            // If we did not receive the location_id (no location rights) get the location_id from the equipment
            if (!$repair->location_id) {
                $repair->location_id = $equipment->location_id;
            }

            $location = Location::findOrFail($repair->location_id);
            $repair->travel_costs = $location->travel_costs;
            $repair->travel_cost_factor = 1;
            $repair->km_costs = $location->km_costs;

            // Add the default rate
            // If we did not receive the customer_id (no customer rights) get the customer_id from the equipment
            if (!$repair->customer_id) {
                $repair->customer_id = $equipment->customer_id;
            }
            $oldStdRate = CustomerRates::getStandardRate($repair->customer_id);

            if ($oldStdRate) {
                $repair->rate_id = $oldStdRate->id;
            }

            $repair->save();

            $invoice = new Invoice();
            $invoice->customer_id = $repair->customer_id;
            $invoice->company_id = 1; // This is one of the Ansent family companies
            /* if(isset($requestRepair['invoice']['order_date'])) */
            $invoice->order_date = isset($requestRepair['invoice']['order_date']) ? $requestRepair['invoice']['order_date'] : null;
            if (isset($requestRepair['invoice']['order_number'])) $invoice->order_number = $requestRepair['invoice']['order_number'];
            if (isset($requestRepair['invoice']['client'])) $invoice->client = $requestRepair['invoice']['client'];

            if ($oldStdRate) {
                $invoice->due_days = $oldStdRate->due_days;
                $invoice->discount_days = $oldStdRate->discount_days;
                $invoice->discount_amount = $oldStdRate->discount_amount;
            } else {
                $invoice->due_days = 14;
                $invoice->discount_days = 0;
                $invoice->discount_amount = 0;
            }
            $repair->invoice()->save($invoice);

            $collection->push(new RepairResource($repair));
        }

        return $collection;
    }

    public function createRelatedRepair(Request $request)
    {
        if (isset($request->id) && $request->id != 0) {
            $parentRepair = Repair::findOrFail($request->id);

            // Get logged-in user
            $user = Auth::user();

            // Generate the number of the repair
            // Start with 60.000, increment by +1,
            // Show a dot as thousand separator - removed the dot separator in AN-515
            $lastRepairNumber = ceil(str_replace('.', '', Repair::getLastRepairNumber()));

            // $newRepairNumber = number_format(++$lastRepairNumber, 0, ',', '.');
            // Request AN-515: remove the dot thousand separator if any
            $newRepairNumber = number_format(++$lastRepairNumber, 0, ',', '');
            if (isset($request->create_related_repair)) $parentRepair->related_repair = $newRepairNumber;

            $repair = new Repair;
            if (isset($request->equipment_id)) $repair->equipment_id = $request->equipment_id;
            if (isset($request->company_id)) $repair->company_id = $request->company_id; // This is one of the Ansent family companies
            if (isset($request->location_id)) $repair->location_id = $request->location_id;
            if (isset($request->customer_id)) $repair->customer_id = $request->customer_id;
            if (isset($request->operator_id)) $repair->operator_id = $request->operator_id;
            $repair->user_id = $user->id;
            $repair->number = $newRepairNumber;

            if (isset($request->status)) $repair->status = $request->status;
            if (isset($request->offer_needed)) $repair->offer_needed = $request->offer_needed;
            if (isset($request->active)) $repair->active = $request->active;

            if (isset($request->work_description)) $repair->work_description = $request->work_description;
            if (isset($request->internal_notes)) $repair->internal_notes = $request->internal_notes;

            // Get the equipment in case there are no right for location, customer or operator
            $equipment = Equipment::findOrFail($parentRepair->equipment_id);

            // If we did not receive the operator_id (no operator rights) get the operator_id from the equipment
            if (!$repair->operator_id) {
                $repair->operator_id = $equipment->operator_id;
            }

            // Add the default travel costs
            // If we did not receive the location_id (no location rights) get the location_id from the equipment
            if (!$repair->location_id) {
                $repair->location_id = $equipment->location_id;
            }

            $location = Location::findOrFail($repair->location_id);
            $repair->travel_costs = $location->travel_costs;
            $repair->km_costs = $location->km_costs;

            // Add the default rate
            // If we did not receive the customer_id (no customer rights) get the customer_id from the equipment
            if (!$repair->customer_id) {
                $repair->customer_id = $equipment->customer_id;
            }
            $oldStdRate = CustomerRates::getStandardRate($repair->customer_id);

            if ($oldStdRate) {
                $repair->rate_id = $oldStdRate->id;
            }

            $repair->save();

            $invoice = new Invoice();
            $invoice->customer_id = $repair->customer_id;
            $invoice->company_id = 1; // This is one of the Ansent family companies
            if ($oldStdRate) {
                $invoice->due_days = $oldStdRate->due_days;
                $invoice->discount_days = $oldStdRate->discount_days;
                $invoice->discount_amount = $oldStdRate->discount_amount;
            } else {
                $invoice->due_days = 14;
                $invoice->discount_days = 0;
                $invoice->discount_amount = 0;
            }
            $repair->invoice()->save($invoice);

            $repair->save();

            $parentRepair->related_repair = $repair->number;
            $parentRepair->related_repair_id = $repair->id;
            $parentRepair->save();

            return new RepairResource($repair);
        } else {
            $apiResponse = array(
                'success' => false
            );
            $apiResponse['errors'] = "repair id missing";

            return $apiResponse;
        }
    }

    public function destroy($id)
    {
        $repair = Repair::findOrFail($id);

        $repair->repairReplacements()->delete();

        if ($repair->delete()) {
            return new RepairResource($repair);
        }
    }

    public function updateSchedule(Request $request)
    {
        if (isset($request->id) && $request->id != 0) {
            $apiData = array(
                'success' => false,
                'message' => 'forbidden_no_permission',
            );

            $repair = Repair::findOrFail($request->id);
            $loggedUser = Auth::user();

            // REPAIRS
            if ($loggedUser->hasAnyPermission(['write repairs', 'write repair_details'])) {
                if (isset($request->rough_schedule_start)) $repair->rough_schedule_start = $request->rough_schedule_start;
                if (isset($request->rough_schedule_end)) $repair->rough_schedule_end = $request->rough_schedule_end;
                if (isset($request->exact_schedule_start)) $repair->exact_schedule_start = $request->exact_schedule_start;
                if (isset($request->exact_schedule_end)) $repair->exact_schedule_end = $request->exact_schedule_end;

                if (isset($request->status)) $repair->status = $request->status;
            }

            $repair->save();

            return new RepairResource($repair);
        } else {
            $apiResponse = array(
                'success' => false
            );
            $apiResponse['errors'] = "repair id missing";

            return $apiResponse;
        }
    }

    public function deleteSchedule(Request $request)
    {
        if (isset($request->id) && $request->id != 0) {
            $apiData = array(
                'success' => false,
                'message' => 'forbidden_no_permission',
            );

            $repair = Repair::findOrFail($request->id);
            $loggedUser = Auth::user();

            // REPAIRS
            if ($loggedUser->hasAnyPermission(['write repairs', 'write repair_details'])) {
                $repair->rough_schedule_start = $request->rough_schedule_start;
                $repair->rough_schedule_end = $request->rough_schedule_end;
                $repair->exact_schedule_start = $request->exact_schedule_start;
                $repair->exact_schedule_end = $request->exact_schedule_end;

                if (isset($request->status)) $repair->status = $request->status;
            }

            $repair->save();

            return new RepairResource($repair);
        } else {
            $apiResponse = array(
                'success' => false
            );
            $apiResponse['errors'] = "repair id missing";

            return $apiResponse;
        }
    }

    public function updateRepairScheduledEmployees(Request $request)
    {
        $apiResponse = array(
            'success' => false
        );

        if (isset($request->id) && $request->id != 0) {
            $apiData = array(
                'success' => false,
                'message' => 'forbidden_no_permission',
            );

            $repair = Repair::findOrFail($request->id);
            $loggedUser = Auth::user();

            // Only Update the scheduled employees if the user has create or write repair details rights
            if (Auth::user()->hasAnyPermission(['create repair_details', 'write repair_details'])) {
                // Sync any scheduled employees changes
                if (isset($request->scheduled_employees)) {
                    $scheduledEmployeesSync = $repair->syncOneToMany($request->scheduled_employees, $repair->scheduledEmployees());
                    error_log("Synced scheduled employees: " . json_encode($scheduledEmployeesSync));
                }
            } else {
                if (!Auth::user()->hasAnyPermission(['create repairs', 'write repairs'])) {
                    $apiData['write repair_details'][] = "scheduled_employees";
                    return response()->json($apiData, 403);
                }
            }

            return new RepairResource($repair);
        } else {
            $apiResponse = array(
                'success' => false
            );
            $apiResponse['errors'] = "repair id missing";

            return $apiResponse;
        }
    }

    public function updateEstimation(Request $request)
    {
        if (isset($request->id) && $request->id != 0) {
            $apiData = array(
                'success' => false,
                'message' => 'forbidden_no_permission',
            );

            $repair = Repair::findOrFail($request->id);
            $loggedUser = Auth::user();

            // REPAIRS
            if ($loggedUser->hasAnyPermission(['write repairs', 'write repair_details'])) {
                if (isset($request->estimation)) $repair->estimation = $request->estimation;
            }

            $repair->save();

            return new RepairResource($repair);
        } else {
            $apiResponse = array(
                'success' => false
            );
            $apiResponse['errors'] = "repair id missing";

            return $apiResponse;
        }
    }
}

<?php

namespace App\Http\Controllers;

use App\Http\Resources\InvoiceCollectionResource;
use App\Http\Resources\InvoicePDFResource;
use App\Http\Resources\InvoiceResource;
use App\Models\Invoice;
use App\Models\Repair;
use App\Traits\Filters\InvoiceFilters;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class InvoiceController extends Controller
{
    public function updatePaymentDate(Request $request)
    {
        $apiResponse = array(
            'success' => false
        );

        if (isset($request->id) && $request->id != 0) {
            $apiData = array(
                'success' => false,
                'message' => 'forbidden_no_permission',
            );

            $invoice = Invoice::findOrFail($request->id);
            $loggedUser = Auth::user();

            // Invoice
            if ($loggedUser->hasPermissionTo('write repair_details') || $loggedUser->hasPermissionTo('create repair_details')) {
                $repair = Repair::find($invoice->repair_id);
                //If we have a repair - update the status to invoice paid
                if ($repair) {
                    if (isset($request->payment_date)) {
                        $invoice->payment_date = $request->payment_date;
                        $repair->status = 'invoice_paid';
                    } else {
                        $invoice->payment_date = null;
                        $repair->status = 'invoice_generated';
                    }
                    $repair->save();
                }

                /* if(isset($request->payment_date)) */
                $invoice->payment_date = isset($request->payment_date) ? $request->payment_date : null;
            }

            $invoice->save();

            return new InvoiceResource($invoice);
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
        $invoice = Invoice::findOrFail($id);

        $invoice->repairReplacements()->delete();

        if ($invoice->delete()) {
            return new InvoiceResource($invoice);
        }
    }

    public function store(Request $request)
    {
        if (isset($request->id)) {
            $invoice = Invoice::findOrFail($request->id);
        } else {
            $invoice = new Invoice();
        }

        // Get logged-in user
        $user = Auth::user();

        $invoice->repair_id = 0;
        if (isset($request->company_id)) $invoice->company_id = $request->company_id;

        // $invoice->generation_date = Carbon::now();
        $invoice->user_id = $user->id;

        if (isset($request->invoice_pdf_generated)) $invoice->invoice_pdf_generated = $request->invoice_pdf_generated;

        if (isset($request->invoice_number_pref)) $invoice->invoice_number_pref = $request->invoice_number_pref;
        if (isset($request->invoice_number_year)) $invoice->invoice_number_year = $request->invoice_number_year;
        if (isset($request->invoice_number_suff)) $invoice->invoice_number_suff = $request->invoice_number_suff;
        if (isset($request->invoice_number)) $invoice->invoice_number = $request->invoice_number;

        // Generate the number of the invoice if not received in the request
        // RA<IDRW>-JJJJ-xxxxx
        // Example: RA04-2020-00001
        if (isset($request->invoice_number) && $request->invoice_number != '') {
            $invoice->invoice_number_suff = $request->invoice_number_suff;
            $invoice->invoice_number = $request->invoice_number;
        } else {
            // Do not create the invoice number on save/update manual invoice
            // $lastInvoiceNumber = Invoice::getLastInvoiceNumberByYear($request->invoice_number_year) + 1;
            // $invoice->invoice_number_suff = $lastInvoiceNumber;
            // $invoice->invoice_number = $invoice->invoice_number_pref.'-'.$invoice->invoice_number_year.'-'.str_pad($invoice->invoice_number_suff, 4, '0', STR_PAD_LEFT);
        }

        if (isset($request->invoice_detailed_number)) $invoice->invoice_detailed_number = $request->invoice_detailed_number;
        /* if(isset($request->invoice_date)) */
        $invoice->invoice_date = isset($request->invoice_date) ? $request->invoice_date : null;
        /* if(isset($request->delivery_date)) */
        $invoice->delivery_date = isset($request->delivery_date) ? $request->delivery_date : null;
        if (isset($request->customer_id)) $invoice->customer_id = $request->customer_id;

        if (isset($request->offer_number)) $invoice->offer_number = $request->offer_number;
        /* if(isset($request->offer_date)) */
        $invoice->offer_date = isset($request->offer_date) ? $request->offer_date : null;
        /* if(isset($request->order_date)) */
        $invoice->order_date = isset($request->order_date) ? $request->order_date : null;
        if (isset($request->order_number)) $invoice->order_number = $request->order_number;
        if (isset($request->client)) $invoice->client = $request->client;

        if (isset($request->due_days)) $invoice->due_days = $request->due_days;
        if (isset($request->discount_days)) $invoice->discount_days = $request->discount_days;
        if (isset($request->discount_amount)) $invoice->discount_amount = $request->discount_amount;

        if ($invoice->invoice_date) {
            $invoice->due_date = Carbon::createFromFormat('Y-m-d', $invoice->invoice_date)->addDays($request->due_days);
            $invoice->discount_date = Carbon::createFromFormat('Y-m-d', $invoice->invoice_date)->addDays($request->discount_days);
        }

        $invoice->save();

        if (isset($request->extra_services)) {
            $extraServicesSync = $invoice->syncOneToMany($request->extra_services, $invoice->extraServices());
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

        $invoice->total = $invoice->es_price + $invoice->rr_price + $invoice->wh_price + $invoice->dr_price;
        error_log("MI TOTAL Price: " . $invoice->total);
        $vat = Vat::getVATForDate($invoice->delivery_date ? $invoice->delivery_date : Carbon::now());
        error_log("MI VAT: " . $vat);
        $invoice->vat = $vat;
        $invoice->total_vat = $invoice->total * $vat;
        error_log("MI TOTAL VAT: " . $invoice->total_vat);
        $invoice->total_with_vat = $invoice->total + $invoice->total_vat;
        error_log("MI TOTAL price with VAT: " . $invoice->total_with_vat);

        $invoice->save();

        return new InvoiceResource(Invoice::findOrFail($invoice->id));
    }

    public function generateInvoicesForIds(Request $request)
    {
        $collection = \Illuminate\Database\Eloquent\Collection::make();

        foreach ($request->invoices as $requestInvoiceId) {
            $invoice = Invoice::findOrFail($requestInvoiceId);

            // Get logged-in user
            $loggedUser = Auth::user();

            // We have a regular invoice - with corresponding repair
            if ($invoice->repair_id) {
                $repair = Repair::findOrFail($invoice->repair_id);
                $invoice = $repair->invoice;
                $customer = $invoice->customer;

                $repair->status = 'invoice_generated';

                // If the repair date was not set already then set it as today when the invoice pdf is generated
                if (!$repair->repair_date) $repair->repair_date = Carbon::now();

                if ($invoice) {
                    $invoice->invoice_pdf_generated = 1;
                    $invoice->generation_date = Carbon::now();
                    $invoice->user_id = $loggedUser->id;

                    $invoice->invoice_date = $invoice->invoice_date ? $invoice->invoice_date : Carbon::now()->format('Y-m-d');

                    $invoice->invoice_detailed_number = $invoice->invoice_detailed_number && $invoice->invoice_detailed_number != '' ? $invoice->invoice_detailed_number : 'R-' . str_replace('.', '', $repair->number) . '-' . ($repair && $repair->repair_date ? Carbon::createFromFormat('Y-m-d', $repair->repair_date)->format('Y-m') : Carbon::now()->format('Y-m'));

                    $invoice->due_days = $invoice->due_days ? $invoice->due_days : 0;
                    $invoice->discount_days = $invoice->discount_days ? $invoice->discount_days : 0;
                    $invoice->discount_amount = $invoice->discount_amount ? $invoice->discount_amount : 0;

                    // Get customer for repair
                    $invoicePrefix = $customer && $customer->invoice_prefix ? 'RA' . $customer->invoice_prefix : 'RAXX';
                    $invoice->invoice_number_pref = $invoice->invoice_number_pref && $invoice->invoice_number_pref != '' ? $invoice->invoice_number_pref : $invoicePrefix;
                    $invoice->invoice_number_year = $invoice->invoice_number_year && $invoice->invoice_number_year != 'YYYY' && $invoice->invoice_number_year != '' ? $invoice->invoice_number_year : Carbon::now()->format('Y');

                    // Generate the number of the invoice if not received in the request
                    // RA<IDRW>-JJJJ-xxxxx
                    // Example: RA04-2020-00001
                    if ($invoice->invoice_number && $invoice->invoice_number != '') {
                        $invoice->invoice_number_suff = $invoice->invoice_number_suff;
                        $invoice->invoice_number = $invoice->invoice_number;
                    } else {
                        $lastInvoiceNumber = Invoice::getLastInvoiceNumberByYear($invoice->invoice_number_year) + 1;
                        $invoice->invoice_number_suff = $lastInvoiceNumber;
                        $invoice->invoice_number = $invoice->invoice_number_pref . '-' . $invoice->invoice_number_year . '-' . str_pad($invoice->invoice_number_suff, 4, '0', STR_PAD_LEFT);
                    }

                    if ($invoice->invoice_date) {
                        $invoice->due_date = Carbon::createFromFormat('Y-m-d', $invoice->invoice_date)->addDays($invoice->due_days);
                        $invoice->discount_date = Carbon::createFromFormat('Y-m-d', $invoice->invoice_date)->addDays($invoice->discount_days);
                    }

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

                    $repair->save();
                }

            } else { // We have a manual invoice to be generated
                if ($invoice) {
                    $invoice->invoice_pdf_generated = 1;
                    $invoice->generation_date = Carbon::now();
                    $invoice->user_id = $loggedUser->id;

                    $invoice->invoice_date = $invoice->invoice_date ? $invoice->invoice_date : Carbon::now()->format('Y-m-d');

                    $invoice->due_days = $invoice->due_days ? $invoice->due_days : 0;
                    $invoice->discount_days = $invoice->discount_days ? $invoice->discount_days : 0;
                    $invoice->discount_amount = $invoice->discount_amount ? $invoice->discount_amount : 0;

                    // Get customer for repair
                    $invoicePrefix = $invoice->customer && $invoice->customer->invoice_prefix ? 'SL' . $invoice->customer->invoice_prefix : 'SLXX';
                    $invoice->invoice_number_pref = $invoice->invoice_number_pref && $invoice->invoice_number_pref != '' ? $invoice->invoice_number_pref : $invoicePrefix;
                    $invoice->invoice_number_year = $invoice->invoice_number_year && $invoice->invoice_number_year != '' && $invoice->invoice_number_year != 'YYYY' ? $invoice->invoice_number_year : Carbon::now()->format('Y');

                    // Generate the number of the invoice if not received in the request
                    // RA<IDRW>-JJJJ-xxxxx
                    // Example: RA04-2020-00001
                    if ($invoice->invoice_number && $invoice->invoice_number != '') {
                        $invoice->invoice_number_suff = $invoice->invoice_number_suff;
                        $invoice->invoice_number = $invoice->invoice_number;
                    } else {
                        $lastInvoiceNumber = Invoice::getLastInvoiceNumberByYear($invoice->invoice_number_year) + 1;
                        $invoice->invoice_number_suff = $lastInvoiceNumber;
                        $invoice->invoice_number = $invoice->invoice_number_pref . '-' . $invoice->invoice_number_year . '-' . str_pad($invoice->invoice_number_suff, 4, '0', STR_PAD_LEFT);
                    }

                    if ($invoice->invoice_date) {
                        $invoice->due_date = Carbon::createFromFormat('Y-m-d', $invoice->invoice_date)->addDays($invoice->due_days);
                        $invoice->discount_date = Carbon::createFromFormat('Y-m-d', $invoice->invoice_date)->addDays($invoice->discount_days);
                    }

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

                    $invoice->setTotalWithVatForDate($invoice->delivery_date ? $invoice->delivery_date : Carbon::now());

                    $invoice->save();
                }
            }

            $collection->push(new InvoiceResource($invoice));
        }

        return $collection;
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

            $invoice = Invoice::findOrFail($request->id);
            $loggedUser = Auth::user();
            // This ones should not be changed during updates
            // if(isset($request->user_id)) $invoice->user_id = $request->user_id;
            // if(isset($request->invoice_number)) $invoice->invoice_number = $request->invoice_number;

            // if($loggedUser->hasPermissionTo('write repairs'))
            // if($loggedUser->hasAnyPermission(['access_internal_notes', 'read repairs']))
            // if($loggedUser->hasAllPermissions(['access_internal_notes', 'read repairs']))

            // Invoice
            if ($loggedUser->hasPermissionTo('write repair_details') || $loggedUser->hasPermissionTo('create repair_details')) {
                /* if(isset($request->payment_date)) */
                $invoice->payment_date = isset($request->payment_date) ? $request->payment_date : null;
                // if(isset($request->invoicing_needed)) $invoice->invoicing_needed = ($request->invoicing_needed ? 1 : 0);
                // if(isset($request->travel_costs) && $loggedUser->hasPermissionTo('access_prices_offer')) $invoice->travel_costs = number_format(floatval($request->travel_costs), 2);
            }

            $invoice->save();


            // Only Update the working hours and the repair replacements if the user has create or write repair details rights
            if (Auth::user()->hasAnyPermission(['create repair_details', 'write repair_details'])) {
                // Sync any extra services
                if (isset($request->extra_services)) {
                    $extraServicesSync = $invoice->syncOneToMany($request->extra_services, $invoice->extraServices());
                    error_log("Synced extra services: " . json_encode($extraServicesSync));
                }
            } else {
                if (!Auth::user()->hasAnyPermission(['create repairs', 'write repairs'])) {
                    $apiData['write repair_details'][] = "extra_services";
                    return response()->json($apiData, 403);
                }
            }

            return new InvoiceResource($invoice);
        } else {
            $apiResponse = array(
                'success' => false
            );
            $apiResponse['errors'] = "repair id missing";

            return $apiResponse;
        }
    }

    public function getInvoicesForPdfByIds(Request $request)
    {
        $query = Invoice::query();
        if (isset($request->invoices) && $request->invoices != "") {
            $query->whereIn('id', $request->invoices);
        }

        $invoices = $query->get();
        error_log("Invoices: " . json_encode(InvoicePDFResource::collection($invoices)));

        return InvoicePDFResource::collection($invoices);
    }

    public function getInvoicesByIds(Request $request)
    {
        $query = Invoice::query();
        if (isset($request->invoices) && $request->invoices != "") {
            $query->whereIn('id', $request->invoices);
        }

        $invoices = $query->get();
        error_log("Invoices: " . json_encode(InvoiceResource::collection($invoices)));

        return InvoiceResource::collection($invoices);
    }

    public function showP(Request $request)
    {
        return new InvoiceResource(Invoice::findOrFail($request->id));
    }

    public function show($id)
    {
        return new InvoiceResource(Invoice::findOrFail($id));
    }

    public function getAll(Request $request, InvoiceFilters $filters)
    {
        $query = Invoice::query();
        if (isset($request->repair_id) && $request->repair_id != "") {
            $query->where('repair_id', $request->repair_id);
        }

        $invoices = $query->filter($filters)->paginate($request->pagination["per_page"],
            ['*'], 'page', $request->pagination["current_page"]);

        return new InvoiceCollectionResource($invoices);
    }

    public function getInvoiceForPDF(Request $request): InvoicePDFResource
    {
        $query = Invoice::query();
        if (isset($request->id) && $request->id != "") {
            $query->where('invoice_number', $request->id);
        }

        $invoice = $query->first();
        error_log("Invoices: " . json_encode(new InvoicePDFResource($invoice)));
        return new InvoicePDFResource($invoice);
    }
}

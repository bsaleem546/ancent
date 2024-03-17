<?php

namespace App\Http\Controllers;

use App\Http\Resources\InvoicePDFResource;
use App\Models\Invoice;
use Illuminate\Http\Request;

class InvoiceController extends Controller
{
    public function getInvoiceForPDF(Request $request): InvoicePDFResource
    {
        $query = Invoice::query();
        if (isset($request->id) && $request->id != "") {
            $query->where('invoice_number', $request->id);
        }

        $invoice = $query->first();
        error_log("Invoices: ". json_encode(new InvoicePDFResource($invoice)));
        return new InvoicePDFResource($invoice);
    }
}

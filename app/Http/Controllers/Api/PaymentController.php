<?php


namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use App\Models\UnitPaymentInstallment;
use App\Models\UnitPaymentInstallmentInvoice;
use App\Events\InvoiceUploaded;

class PaymentController extends Controller
{
    /**
     * Upload an invoice for an installment.
     *
     * POST /api/installments/{installment}/invoices
     * Body: file: invoice, paid_amount: decimal
     */
    public function uploadInvoice(Request $request, UnitPaymentInstallment $installment)
    {
        $data = $request->validate([
            'invoice' => 'required|file|mimes:jpg,jpeg,png,pdf|max:5120',
            'paid_amount' => 'required|numeric|min:0.01',
        ]);

        // store file
        $path = $request->file('invoice')->store('invoices', 'public');

        // create invoice record (default status = 'pending')
        $invoice = UnitPaymentInstallmentInvoice::create([
            'unit_payment_installment_id' => $installment->id,
            'paid_amount' => $data['paid_amount'],
            'invoice_file' => $path,
            'payment_date' => now(), // treat upload time as payment_date; change if needed
            'status' => 'pending', // auto-confirm listener will set 'confirmed'
        ]);

        // dispatch event so listeners can handle it (auto-confirm + recalc)
        event(new InvoiceUploaded($invoice));

        return response()->json([
            'message' => 'Invoice uploaded successfully and under processing.',
            'data' => $invoice,
        ], 201);
    }
}

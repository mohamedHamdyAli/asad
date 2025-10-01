<?php
/**
 * Admin (Dashboard) controller for installments operations.
 *
 * - updateStatus: Manually update an installment's status (pending/paid/overdue).
 * - confirmInvoice: Admin confirms or rejects a pending invoice (then InvoiceStatusChanged is fired).
 *
 * Routes example:
 *  PUT /admin/installments/{installment}/status
 *  POST /admin/invoices/{invoice}/confirm  (body: action = confirm|reject)
 */

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\UnitPaymentInstallment;
use App\Models\UnitPaymentInstallmentInvoice;
use App\Events\InvoiceStatusChanged;
use App\Events\PaymentStatusChanged;

class UnitPaymentInstallmentController extends Controller
{
    /**
     * Update installment status from admin panel.
     */
    public function updateStatus(Request $request, UnitPaymentInstallment $installment)
    {
        $request->validate([
            'status' => 'required|in:pending,paid,overdue',
        ]);

        $oldStatus = $installment->status;
        $installment->status = $request->status;
        $installment->save();

        event(new PaymentStatusChanged($installment, $oldStatus, $request->status));

        return response()->json([
            'message' => 'Installment status updated successfully',
            'data' => $installment,
        ]);
    }

    /**
     * Admin confirm/reject a pending invoice.
     *
     * Body: action => 'confirm' | 'reject'
     */
    public function confirmInvoice(Request $request, UnitPaymentInstallmentInvoice $invoice)
    {
        $request->validate([
            'action' => 'required|in:confirm,reject',
        ]);

        $oldStatus = $invoice->status;
        $newStatus = $request->action === 'confirm' ? 'confirmed' : 'rejected';

        $invoice->status = $newStatus;
        if ($newStatus === 'confirmed') {
            $invoice->payment_date ??= now();
        }
        $invoice->save();

        event(new InvoiceStatusChanged($invoice, $oldStatus, $newStatus));

        return response()->json([
            'message' => "Invoice {$newStatus} successfully",
            'data' => $invoice,
        ]);
    }
}

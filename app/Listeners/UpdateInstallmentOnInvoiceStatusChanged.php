<?php
/**
 * Listener: UpdateInstallmentOnInvoiceStatusChanged
 *
 * Handles InvoiceStatusChanged event.
 * Responsibilities:
 *  - Recalculate the total confirmed payments for the related installment.
 *  - If total_confirmed >= installment.amount => mark installment status 'paid'.
 *  - Update installment.payment_date to latest confirmed invoice.payment_date (optional).
 *  - Update parent unit_payment remaining_installments and overall_status.
 *  - If an installment's status changed, dispatch PaymentStatusChanged for logging/notifications.
 *
 * This listener encapsulates the business rule for converting invoice confirmations
 * into installment/payment-plan state changes.
 */

namespace App\Listeners;

use App\Events\InvoiceStatusChanged;
use App\Events\PaymentStatusChanged;
use App\Models\UnitPaymentInstallment;
use App\Models\UnitPaymentInstallmentInvoice;
use App\Models\UnitPayment;
use Illuminate\Contracts\Queue\ShouldQueue;

class UpdateInstallmentOnInvoiceStatusChanged implements ShouldQueue
{
    public function handle(InvoiceStatusChanged $event): void
    {
        /** @var UnitPaymentInstallmentInvoice $invoice */
        $invoice = $event->invoice;
        $installment = $invoice->installment()->first();

        if (! $installment) {
            return;
        }

        $oldInstallmentStatus = $installment->status;

        // Sum confirmed invoices for this installment
        $confirmedTotal = UnitPaymentInstallmentInvoice::query()
            ->where('unit_payment_installment_id', $installment->id)
            ->where('status', 'confirmed')
            ->sum('paid_amount');

        // Decide new status for installment
        $newInstallmentStatus = $oldInstallmentStatus;

        if ($confirmedTotal >= floatval($installment->amount)) {
            $newInstallmentStatus = 'paid';
            // set payment_date to latest confirmed invoice date
            $latestPaymentDate = UnitPaymentInstallmentInvoice::query()
                ->where('unit_payment_installment_id', $installment->id)
                ->where('status', 'confirmed')
                ->orderByDesc('payment_date')
                ->value('payment_date');

            $installment->payment_date = $latestPaymentDate ?: $installment->payment_date ?? now();
        } else {
            // remain pending (or could introduce 'partial' if you want)
            $newInstallmentStatus = $oldInstallmentStatus === 'paid' ? 'paid' : 'pending';
        }

        // Persist if changed
        if ($newInstallmentStatus !== $oldInstallmentStatus) {
            $installment->status = $newInstallmentStatus;
            $installment->save();

            // Dispatch payment status changed event
            event(new PaymentStatusChanged($installment, $oldInstallmentStatus, $newInstallmentStatus));
        }

        // Update parent unit payment summary (remaining_installments, overall_status)
        $unitPayment = UnitPayment::query()->find($installment->unit_payment_id);
        if ($unitPayment) {
            $remaining = $unitPayment->installments()->where('status', '!=', 'paid')->count();
            $unitPayment->remaining_installments = $remaining;

            if ($remaining === 0) {
                $unitPayment->overall_status = 'completed';
            } elseif ($unitPayment->installments()->where('status', 'paid')->exists()) {
                $unitPayment->overall_status = 'in_progress';
            } else {
                $unitPayment->overall_status = 'pending';
            }

            $unitPayment->save();
        }
    }
}

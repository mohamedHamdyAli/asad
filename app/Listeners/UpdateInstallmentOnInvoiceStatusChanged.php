<?php

namespace App\Listeners;

use App\Events\InvoiceStatusChanged;
use App\Events\PaymentStatusChanged;
use App\Models\UnitPaymentInstallment;
use App\Models\UnitPaymentInstallmentInvoice;
use App\Models\UnitPayment;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Support\Facades\DB;

class UpdateInstallmentOnInvoiceStatusChanged implements ShouldQueue
{
    public function handle(InvoiceStatusChanged $event): void
    {
        /** @var UnitPaymentInstallmentInvoice $invoice */
        $invoice = $event->invoice;

        // Lock the installment row to avoid race conditions
        $installment = UnitPaymentInstallment::where('id', $invoice->unit_payment_installment_id)
            ->lockForUpdate()
            ->first();

        if (!$installment) {
            return;
        }

        // Use transaction for consistency
        DB::transaction(function () use ($invoice, $installment, $event) {
            $oldInstallmentStatus = $installment->status;

            // Sum confirmed invoices for this installment
            $confirmedTotal = UnitPaymentInstallmentInvoice::query()
                ->where('unit_payment_installment_id', $installment->id)
                ->where('status', 'confirmed')
                ->sum('paid_amount');

            $installmentAmount = (float) $installment->amount;
            $tolerance = 0.01;

            if ($confirmedTotal >= $installmentAmount - $tolerance) {
                $newInstallmentStatus = 'paid';

                // set payment_date to latest confirmed invoice date
                $latestPaymentDate = UnitPaymentInstallmentInvoice::query()
                    ->where('unit_payment_installment_id', $installment->id)
                    ->where('status', 'confirmed')
                    ->orderByDesc('payment_date')
                    ->value('payment_date');

                $installment->payment_date = $latestPaymentDate ?: ($installment->payment_date ?? now());
            } elseif ($confirmedTotal > 0) {
                $newInstallmentStatus = 'partial';
            } else {
                $newInstallmentStatus = 'pending';
            }

            // Persist if changed and dispatch PaymentStatusChanged
            if ($newInstallmentStatus !== $oldInstallmentStatus) {
                $installment->status = $newInstallmentStatus;
                $installment->save();

                event(new PaymentStatusChanged($installment, $oldInstallmentStatus, $newInstallmentStatus));
            }

            // Update parent unit payment summary (remaining_installments, overall_status)
            $unitPayment = UnitPayment::query()->lockForUpdate()->find($installment->unit_payment_id);

            if ($unitPayment) {
                $totalInstallments = $unitPayment->installments()->count();
                $paidInstallments = $unitPayment->installments()->where('status', 'paid')->count();
                $overdueInstallments = $unitPayment->installments()->where('status', 'overdue')->count();
                $remaining = max(0, $totalInstallments - $paidInstallments);

                $unitPayment->remaining_installments = $remaining;

                if ($remaining === 0) {
                    $unitPayment->overall_status = 'completed';
                } elseif ($paidInstallments > 0) {
                    $unitPayment->overall_status = 'in_progress';
                } elseif ($overdueInstallments > 0) {
                    $unitPayment->overall_status = 'overdue';
                } else {
                    $unitPayment->overall_status = 'pending';
                }

                $unitPayment->save();
            }

        });
    }
}

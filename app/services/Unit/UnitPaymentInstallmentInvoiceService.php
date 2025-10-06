<?php

namespace App\services\Unit;

use App\Models\UnitPaymentInstallment;
use App\Models\UnitPaymentInstallmentInvoice;
use Illuminate\Support\Facades\DB;
use App\Events\InvoiceStatusChanged;
use App\Events\PaymentStatusChanged;

class UnitPaymentInstallmentInvoiceService
{
    public function getInvoices($installmentId)
    {
        $installment = UnitPaymentInstallment::with('invoices')->find($installmentId);

        if (!$installment) {
            return ['status' => false, 'message' => 'Installment not found'];
        }

        return ['status' => true, 'data' => $installment->invoices];
    }

    public function updateStatus($installmentId, $status)
    {
        return DB::transaction(function () use ($installmentId, $status) {
            $installment = UnitPaymentInstallment::find($installmentId);

            if (!$installment) {
                return ['status' => false, 'message' => 'Installment not found'];
            }

            $oldStatus = $installment->status;
            $installment->update(['status' => $status]);

            event(new PaymentStatusChanged($installment, $oldStatus, $status));

            return [
                'status' => true,
                'data' => $installment,
                'message' => 'Installment status updated successfully'
            ];
        });
    }

    public function confirmInvoice($request)
    {
        return DB::transaction(function () use ($request) {
            $invoice = UnitPaymentInstallmentInvoice::find($request['invoice_id']);

            if (!$invoice) {
                return ['status' => false, 'message' => 'Invoice not found'];
            }

            $oldStatus = $invoice->status;
            $newStatus = $request['action'] === 'confirm' ? 'confirmed' : 'rejected';

            if ($oldStatus === $newStatus) {
                return [
                    'status' => false,
                    'message' => "Invoice already {$newStatus}",
                    'data' => $invoice
                ];
            }

            $invoice->status = $newStatus;
            if ($newStatus === 'confirmed' && !$invoice->payment_date) {
                $invoice->payment_date = now();
            }

            $invoice->save();

            event(new InvoiceStatusChanged($invoice, $oldStatus, $newStatus));

            return [
                'status' => true,
                'message' => "Invoice {$newStatus} successfully",
                'data' => $invoice
            ];
        });
    }
}

<?php

namespace App\Services\PaymentLogs;

use App\Models\UnitPayment;
use App\Models\UnitPaymentLog;
use App\Models\UnitPaymentInstallment;
use App\Models\UnitPaymentInstallmentInvoice;

class UnitPaymentLogService
{

    public function getUnitLogs($unitId): array
    {
        $paymentIds     = UnitPayment::where('unit_id', $unitId)->pluck('id');
        $installmentIds = UnitPaymentInstallment::whereIn('unit_payment_id', $paymentIds)->pluck('id');
        $invoiceIds     = UnitPaymentInstallmentInvoice::whereIn('unit_payment_installment_id', $installmentIds)->pluck('id');

        if ($paymentIds->isEmpty()) {
            return ['status' => false, 'message' => 'No payments found for this unit', 'data' => []];
        }
        $logs = UnitPaymentLog::query()
            ->where(function ($q) use ($paymentIds, $installmentIds, $invoiceIds) {
                $q->whereIn('model_type', [UnitPayment::class])
                    ->whereIn('model_id', $paymentIds)
                    ->orWhereIn('model_id', $installmentIds)
                    ->where('model_type', UnitPaymentInstallment::class)
                    ->orWhereIn('model_id', $invoiceIds)
                    ->where('model_type', UnitPaymentInstallmentInvoice::class);
            })
            ->latest()
            ->get();

        if ($logs->isEmpty()) {
            return ['status' => false, 'message' => 'No logs found for this unit', 'data' => []];
        }

        return ['status' => true, 'message' => 'Unit payment logs fetched successfully', 'data' => $logs];
    }
    public function getInstallmentLogs($installmentId): array
    {
        $invoiceIds = UnitPaymentInstallmentInvoice::where('unit_payment_installment_id', $installmentId)->pluck('id');

        $logs = UnitPaymentLog::query()
            ->where(function ($q) use ($installmentId, $invoiceIds) {
                $q->where(function ($sub) use ($installmentId) {
                    $sub->where('model_type', UnitPaymentInstallment::class)
                        ->where('model_id', $installmentId);
                })
                    ->orWhere(function ($sub) use ($invoiceIds) {
                        $sub->where('model_type', UnitPaymentInstallmentInvoice::class)
                            ->whereIn('model_id', $invoiceIds);
                    });
            })
            ->latest()
            ->get();

        if ($logs->isEmpty()) {
            return ['status' => false, 'message' => 'No logs found for this installment', 'data' => []];
        }

        return ['status' => true, 'message' => 'Installment logs fetched successfully', 'data' => $logs];
    }
}

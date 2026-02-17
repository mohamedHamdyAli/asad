<?php

namespace App\services\Unit;

use App\Http\Resources\UnitPaymentResource;
use App\Http\Resources\UnitPaymentInstallmentResource;
use App\Models\UnitPayment;
use App\Models\UnitPaymentInstallment;
use App\Models\UnitPaymentInstallmentInvoice;
use App\services\FileService;
use Illuminate\Support\Facades\DB;

class UnitPaymentApiService
{

    public function allInstallments($request)
    {
        $statuses = ['partial', 'overdue', 'unpaid', 'pending', 'paid'];

        $installments = UnitPayment::where('unit_id', $request['unit_id'])->with([
            'installments' => function ($q) use ($statuses) {
                $q->whereIn('status', $statuses);
            }
        ])
            ->whereHas('installments', fn($q) => $q->whereIn('status', $statuses))
            ->get();

                return $installments->isEmpty()
                    ? failReturnMsg('No installments found', 404)
                    : successReturnData(UnitPaymentResource::collection($installments), 'Installments retrieved successfully');
    }

    public function allCompletedInstallments($request)
    {
        $installments = UnitPaymentInstallment::whereHas('paymentPlan', fn($q) => $q->where('unit_id', $request['unit_id']))
            ->with(['paymentPlan', 'invoices'])
            ->get();

        if ($installments->isEmpty()) {
            return failReturnMsg('No installments found', 404);
        }

        $paymentPlan = $installments->first()->paymentPlan;

        return successReturnData([
            'unit_id' => $paymentPlan->unit_id,
            'total_price' => $paymentPlan->total_price,
            'installments' => UnitPaymentInstallmentResource::collection($installments),
        ], 'Installments retrieved successfully');
    }

    public function activeInstallments($request)
    {
        $statuses = ['partial', 'overdue', 'unpaid', 'pending'];

        $installments = UnitPayment::where('unit_id', $request['unit_id'])->with([
            'installments' => function ($q) use ($statuses) {
                $q->whereIn('status', $statuses)
                    ->whereDate('due_date', '>=', now());
            }
        ])
            ->whereHas('installments', function ($q) use ($statuses) {
                $q->whereIn('status', $statuses)
                    ->whereDate('due_date', '>=', now());
            })
            ->first();

        return $installments
            ? successReturnData(new UnitPaymentResource($installments), 'Active installments retrieved successfully')
            : failReturnMsg('No active installments found', 404);
    }



    public function viewInvoice(UnitPaymentInstallmentInvoice $invoice)
    {
        $path = storage_path('app/public/' . $invoice->invoice_file);

        if (!file_exists($path)) {
            return failReturnMsg('Invoice file not found', 404);
        }

        return response()->file($path);
    }

    public function uploadInvoice($request, $installment)
    {
        return DB::transaction(function () use ($request, $installment) {

            if ($request['paid_amount'] > $installment->amount) {
                return failReturnMsg("Paid amount cannot exceed installment amount of {$installment->amount}");
            }

            // upload file
            $invoiceFilePath = FileService::upload($request['invoice_file'], 'units/invoices');

            // create invoice record (default status = 'pending')
            $invoice = UnitPaymentInstallmentInvoice::create([
                'unit_payment_installment_id' => $installment->id,
                'paid_amount' => $request['paid_amount'],
                'invoice_file' => $invoiceFilePath,
                'payment_date' => now(),
                'status' => 'pending',
            ]);

            // Update installment status if needed
            if (in_array($installment->status, ['unpaid', 'overdue'], true)) {
                $installment->update(['status' => 'pending']);
            }

            // $unitPayment = UnitPayment::find($installment->unit_payment_id);
            // if ($unitPayment && $unitPayment->remaining_installments > 0) {
            //     $unitPayment->decrement('remaining_installments');
            // }

            return successReturnData($invoice, 'Invoice uploaded successfully and is under processing');

        });
    }
}

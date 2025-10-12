<?php

namespace App\services\Unit;

use App\Http\Resources\UnitPaymentResource;
use App\Models\UnitPayment;
use App\Models\UnitPaymentInstallmentInvoice;
use App\services\FileService;
use Illuminate\Support\Facades\DB;

class UnitPaymentApiService
{

    public function allInstallments($request)
    {
        $statuses = ['partial', 'overdue', 'unpaid'];

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
        $installments = UnitPayment::where('unit_id', $request['unit_id'])->with([
            'installments' => fn($q) => $q->where('status', 'paid')
        ])->get();

        return $installments->isEmpty()
            ? failReturnMsg('No installments found', 404)
            : successReturnData(UnitPaymentResource::collection($installments), 'Installments retrieved successfully');
    }

    public function activeInstallments($request)
    {
        $statuses = ['partial', 'overdue', 'unpaid'];

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



    public function uploadInvoice($request, $installment)
    {
        return DB::transaction(function () use ($request, $installment) {

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

            return successReturnData($invoice, 'Invoice uploaded successfully and is under processing');

        });
    }
}

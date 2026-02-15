<?php


namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\Api\InvoiceRequest;
use App\Http\Requests\Api\UnitIdRequest;
use App\services\Unit\UnitPaymentApiService;
use App\Models\UnitPaymentInstallment;
use App\Models\UnitPaymentInstallmentInvoice;
use Illuminate\Support\Facades\Validator;

class PaymentController extends Controller
{
    private UnitPaymentApiService $paymentService;

    public function __construct(UnitPaymentApiService $paymentService)
    {
        $this->paymentService = $paymentService;
    }

    public function allInstallments(UnitIdRequest $request)
    {
        return $this->paymentService->allInstallments($request->validated());
    }
    public function allCompletedInstallments(UnitIdRequest $request)
    {
        if (!userAuth()) {
            return failReturnMsg('Unauthorized', 401);
        }

        return $this->paymentService->allCompletedInstallments($request->validated());
    }
    public function activeInstallments(UnitIdRequest $request)
    {
        return $this->paymentService->activeInstallments($request->validated());
    }

    public function viewInvoice()
    {
        $validator = Validator::make(request()->all(), [
            'invoice_id' => 'required|exists:unit_payment_installment_invoices,id',
        ], [
            'invoice_id.required' => 'Invoice ID is required',
            'invoice_id.exists' => 'Invoice not found',
        ]);

        if ($validator->fails()) {
            return failReturnMsg($validator->errors()->first());
        }

        $invoice = UnitPaymentInstallmentInvoice::find(request('invoice_id'));
        return $this->paymentService->viewInvoice($invoice);
    }

    public function uploadInvoice(InvoiceRequest $request, UnitPaymentInstallment $installment)
    {
        return $this->paymentService->uploadInvoice($request->validated(), $installment);
    }
}

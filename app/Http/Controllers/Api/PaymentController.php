<?php


namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\Api\InvoiceRequest;
use App\Http\Requests\Api\UnitIdRequest;
use App\services\Unit\UnitPaymentApiService;
use App\Models\UnitPaymentInstallment;

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
        return $this->paymentService->allCompletedInstallments($request->validated());
    }
    public function activeInstallments(UnitIdRequest $request)
    {
        return $this->paymentService->activeInstallments($request->validated());
    }

    public function uploadInvoice(InvoiceRequest $request, UnitPaymentInstallment $installment)
    {
        return $this->paymentService->uploadInvoice($request->validated(), $installment);
    }
}

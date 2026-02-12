<?php

namespace App\Http\Controllers\Admin;
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
use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\InstallmentStatusRequest;
use App\Http\Requests\Admin\InvoiceConfirmRequest;
use App\services\Unit\UnitPaymentInstallmentInvoiceService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class UnitPaymentInstallmentController extends Controller
{
    private UnitPaymentInstallmentInvoiceService $invoiceService;

    public function __construct(UnitPaymentInstallmentInvoiceService $invoiceService)
    {
        $this->invoiceService = $invoiceService;
    }

    public function getInvoices($installmentId): JsonResponse
    {
        $result = $this->invoiceService->getInvoices($installmentId);

        return response()->json([
            'status' => $result['status'] ? 'success' : 'error',
            'message' => $result['message'] ?? ($result['status'] ? 'Invoices fetched successfully' : 'Failed to fetch invoices'),
            'data' => $result['data'] ?? null,
        ], $result['status'] ? 200 : 404);
    }

    public function updateStatus(InstallmentStatusRequest $request, $installmentId): JsonResponse
    {
        $result = $this->invoiceService->updateStatus($installmentId, $request->status);

        return response()->json([
            'status' => $result['status'] ? 'success' : 'error',
            'message' => $result['message'] ?? ($result['status'] ? 'Status updated successfully' : 'Failed to update status'),
            'data' => $result['data'] ?? null,
        ], $result['status'] ? 200 : 404);
    }

    public function uploadInvoice(Request $request, $installmentId): JsonResponse
    {
        $request->validate([
            'invoice_file' => 'required|file|mimes:jpg,jpeg,png,pdf|max:5120',
            'paid_amount' => 'required|numeric|min:0.01',
            'payment_date' => 'nullable|date',
        ]);

        $result = $this->invoiceService->uploadInvoice($request->all(), $installmentId);

        return response()->json([
            'status' => $result['status'] ? 'success' : 'error',
            'message' => $result['message'],
            'data' => $result['data'] ?? null,
        ], $result['status'] ? 200 : 422);
    }

     public function confirmInvoice(InvoiceConfirmRequest $request): JsonResponse
    {
        $result = $this->invoiceService->confirmInvoice($request->validated());

        return response()->json([
            'status'  => $result['status'] ? 'success' : 'error',
            'message' => $result['message'],
            'data'    => $result['data'] ?? null,
        ], $result['status'] ? 200 : 404);
    }
}

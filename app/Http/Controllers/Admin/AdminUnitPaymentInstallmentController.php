<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\UnitPaymentInstallmentRequest;
use App\services\Unit\UnitPaymentInstallmentCrudService;
use Illuminate\Http\JsonResponse;

class AdminUnitPaymentInstallmentController extends Controller
{
    private UnitPaymentInstallmentCrudService $installmentService;

    public function __construct(UnitPaymentInstallmentCrudService $installmentService)
    {
        $this->installmentService = $installmentService;
    }

    public function store(UnitPaymentInstallmentRequest $request): JsonResponse
    {
        $result = $this->installmentService->createInstallment($request->validated());

        if (!$result['status']) {
            return response()->json([
                'status' => 'error',
                'message' => $result['message'] ?? 'Failed to create installment',
            ], 404);
        }

        return response()->json([
            'status' => 'success',
            'message' => 'Installment created successfully',
        ], 201);
    }


    public function update(UnitPaymentInstallmentRequest $request, $id): JsonResponse
    {
        $result = $this->installmentService->updateInstallment($id, $request->validated());

        if (!$result['status']) {
            return response()->json([
                'status' => 'error',
                'message' => $result['message'] ?? 'Installment not found',
            ], 404);
        }

        return response()->json([
            'status' => 'success',
            'message' => 'Installment updated successfully',
            'data' => $result['data'],
        ], 200);
    }


     public function destroy($id): JsonResponse
    {
        $result = $this->installmentService->deleteInstallment($id);

        if (!$result['status']) {
            return response()->json([
                'status' => 'error',
                'message' => $result['message'] ?? 'Installment not found',
            ], 404);
        }

        return response()->json([
            'status' => 'success',
            'message' => 'Installment deleted successfully',
        ], 200);
    }

    public function show($unitPaymentId): JsonResponse
{
    $result = $this->installmentService->getInstallmentsByUnitPayment($unitPaymentId);

    if (!$result['status']) {
        return response()->json([
            'status' => 'error',
            'message' => $result['message'] ?? 'Unit payment not found',
        ], 404);
    }

    return response()->json([
        'status' => 'success',
        'message' => 'Installments fetched successfully',
        'data' => $result['data'],
    ], 200);
}

}

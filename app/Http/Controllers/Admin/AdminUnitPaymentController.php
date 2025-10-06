<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\UnitPaymentRequest;
use App\services\Unit\UnitPaymentCrudService;
use Illuminate\Http\JsonResponse;

class AdminUnitPaymentController extends Controller
{
    private UnitPaymentCrudService $unitPaymentCrudService;

    public function __construct(UnitPaymentCrudService $unitPaymentCrudService)
    {
        $this->unitPaymentCrudService = $unitPaymentCrudService;
    }


    public function index($unitId): JsonResponse
    {
        $data = $this->unitPaymentCrudService->getUnitPayments($unitId);

        return response()->json([
            'status' => 'success',
            'message' => 'Unit payments fetched successfully',
            'data' => $data,
        ], 200);
    }

    public function store(UnitPaymentRequest $request): JsonResponse
    {
        $created = $this->unitPaymentCrudService->createUnitPayments($request->validated());

        return response()->json([
            'status' => 'success',
            'message' => 'Unit payments created successfully',
        ], 201);
    }
    public function update(UnitPaymentRequest $request, $id): JsonResponse
    {
        $updated = $this->unitPaymentCrudService->updateUnitPayments($id, $request->validated());

        if (!$updated) {
            return response()->json([
                'status' => 'error',
                'message' => 'Unit payment not found',
            ], 404);
        }

        return response()->json([
            'status' => 'success',
            'message' => 'Unit payment updated successfully',
        ], 200);
    }



    public function destroy($id): JsonResponse
    {
        $deleted = $this->unitPaymentCrudService->deleteUnitPayment($id);

        if (!$deleted) {
            return response()->json([
                'status' => 'error',
                'message' => 'Unit payment not found',
            ], 404);
        }

        return response()->json([
            'status' => 'success',
            'message' => 'Unit payment deleted successfully',
        ], 200);
    }
}

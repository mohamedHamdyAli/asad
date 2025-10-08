<?php

namespace App\Http\Controllers\Admin;

use Illuminate\Http\JsonResponse;
use App\Http\Controllers\Controller;
use App\services\PaymentLogs\UnitPaymentLogService;

class AdminUnitPaymentLogController extends Controller
{
    private UnitPaymentLogService $logService;

    public function __construct(UnitPaymentLogService $logService)
    {
        $this->logService = $logService;
    }

    public function getUnitLogs($unitId): JsonResponse
    {
        $result = $this->logService->getUnitLogs($unitId);

        return response()->json([
            'status'  => $result['status'] ? 'success' : 'error',
            'message' => $result['message'],
            'data'    => $result['data'] ?? [],
        ], $result['status'] ? 200 : 404);
    }

    public function getInstallmentLogs($installmentId): JsonResponse
    {
        $result = $this->logService->getInstallmentLogs($installmentId);

        return response()->json([
            'status'  => $result['status'] ? 'success' : 'error',
            'message' => $result['message'],
            'data'    => $result['data'] ?? [],
        ], $result['status'] ? 200 : 404);
    }
}

<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Services\Unit\UnitLiveCameraService;

class UnitLiveCameraController extends Controller
{
    // protected UnitLiveCameraService $unitLiveCameraService;

    // public function __construct(UnitLiveCameraService $unitLiveCameraService)
    // {
    //     $this->unitLiveCameraService = $unitLiveCameraService;
    // }

    // public function getLiveStreamLink($unitId)
    // {
    //     $result = $this->unitLiveCameraService->getLiveStreamLink($unitId);
    //     return response()->json($result);
    // }

    // public function viewStream($unitId)
    // {
    //     $result = $this->unitLiveCameraService->getLiveStreamLink($unitId);
    //     if (!$result['status']) {
    //         return response('Camera not found', 404);
    //     }

    //     $streamUrl = $result['data']['stream_url'];
    //     return view('live', compact('streamUrl'));
    // }
}

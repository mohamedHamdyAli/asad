<?php

namespace App\Http\Controllers\Admin\Unit;

use App\Http\Controllers\Controller;
use App\services\Unit\UnitLiveCameraCrudService;
use App\Http\Requests\Admin\UnitLiveCameraRequest;

class UnitLiveCameraController extends Controller
{
   private UnitLiveCameraCrudService $cameraService;

    public function __construct(UnitLiveCameraCrudService $cameraService)
    {
        $this->cameraService = $cameraService;
    }

    public function index($unitId)
    {
        $data = $this->cameraService->getUnitLiveCameras($unitId);
        return response()->json([
            'status' => 'success',
            'message' => 'Unit live cameras fetched successfully',
            'data' => $data,
        ], 200);
    }

    public function store(UnitLiveCameraRequest $request)
    {
        $this->cameraService->createUnitLiveCameras($request->validated());
        return response()->json([
            'status' => 'success',
            'message' => 'Unit live cameras created successfully',
        ], 201);
    }

    public function update(UnitLiveCameraRequest $request)
    {
        $this->cameraService->updateUnitLiveCameras($request->validated());
        return response()->json([
            'status' => 'success',
            'message' => 'Unit live cameras updated successfully',
        ], 200);
    }

    public function destroy($id)
    {
        $this->cameraService->deleteUnitLiveCamera($id);
        return response()->json([
            'status' => 'success',
            'message' => 'Unit live camera deleted successfully',
        ], 200);
    }
}

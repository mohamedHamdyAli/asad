<?php

namespace App\Http\Controllers\Admin\Unit;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\UnitDrawingRequest;
use App\services\Unit\UnitDrawingCrudService;

class DrawingsController extends Controller
{
    private UnitDrawingCrudService $unitDrawingService;

    public function __construct(UnitDrawingCrudService $unitDrawingService)
    {
        $this->unitDrawingService = $unitDrawingService;
    }

        public function index($unitId)
    {
        $data = $this->unitDrawingService->getUnitDrawingsData($unitId);
        return response()->json([
            'status' => 'success',
            'message' => 'Unit Drawings fetched successfully',
            'data' => $data
        ], 200);
    }

    public function store(UnitDrawingRequest $request)
    {
        $this->unitDrawingService->createUnitDrawings($request->validated());
        return response()->json([
            'status' => 'success',
            'message' => 'Unit Drawings created successfully',
        ], 201);
    }

    public function update(UnitDrawingRequest $request)
    {
        $this->unitDrawingService->updateUnitDrawingsData($request->validated());
        return response()->json([
            'status' => 'success',
            'message' => 'Unit Drawings updated successfully'
        ], 200);
    }

    public function destroy($id)
    {
        $this->unitDrawingService->deleteUnitDrawing($id);
        return response()->json([
            'status' => 'success',
            'message' => 'Unit Drawing deleted successfully'
        ], 200);
    }
}

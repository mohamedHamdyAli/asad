<?php

namespace App\Http\Controllers\Admin\Unit;

use App\Http\Controllers\Controller;
use App\services\Unit\UnitConsultantCrudService;
use App\Http\Requests\Admin\UnitConsultantRequest;

class UnitConsultantController extends Controller
{
    private UnitConsultantCrudService $unitConsultantCrudService;

    public function __construct(UnitConsultantCrudService $unitConsultantCrudService)
    {
        $this->unitConsultantCrudService = $unitConsultantCrudService;
    }

    public function index($unitId)
    {
        $data = $this->unitConsultantCrudService->getUnitConsultants($unitId);
        return response()->json([
            'status' => 'success',
            'message' => 'Unit consultants fetched successfully',
            'data' => $data
        ], 200);
    }

    public function store(UnitConsultantRequest $request)
    {
        $this->unitConsultantCrudService->createUnitConsultants($request->validated());
        return response()->json([
            'status' => 'success',
            'message' => 'Unit consultants created successfully',
        ], 201);
    }

    public function update(UnitConsultantRequest $request)
    {
        $this->unitConsultantCrudService->updateUnitConsultants($request->validated());
        return response()->json([
            'status' => 'success',
            'message' => 'Unit consultants updated successfully',
        ], 200);
    }

    public function destroy($id)
    {
        $this->unitConsultantCrudService->deleteUnitConsultant($id);
        return response()->json([
            'status' => 'success',
            'message' => 'Unit consultant deleted successfully',
        ], 200);
    }
}

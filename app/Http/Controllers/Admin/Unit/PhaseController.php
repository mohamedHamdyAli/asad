<?php

namespace App\Http\Controllers\Admin\Unit;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\UnitPhaseRequest;
use App\services\Unit\UnitPhaseCrudService;

class PhaseController extends Controller
{
    private UnitPhaseCrudService $service;

    public function __construct(UnitPhaseCrudService $service)
    {
        $this->service = $service;
    }

    public function index($unitId)
    {
        $data = $this->service->getUnitPhases($unitId);
        return response()->json([
            'status' => 'success',
            'message' => 'Unit phases fetched successfully',
            'data' => $data
        ], 200);
    }

    public function store(UnitPhaseRequest $request)
    {
        $this->service->createPhases($request->validated());
        return response()->json([
            'status' => 'success',
            'message' => 'Unit phases created successfully',
        ], 201);
    }

    public function update(UnitPhaseRequest $request)
    {
        $this->service->updatePhases($request->validated());
        return response()->json([
            'status' => 'success',
            'message' => 'Unit phases updated successfully',
        ], 200);
    }

    public function destroy($id)
    {
        $this->service->deletePhase($id);
        return response()->json([
            'status' => 'success',
            'message' => 'Unit phase deleted successfully',
        ], 200);
    }
}

<?php

namespace App\Http\Controllers\Admin\Unit;

use App\Http\Controllers\Controller;
use App\Services\Unit\UnitCrudService;
use App\Http\Requests\Admin\UnitRequest;

class UnitController extends Controller
{
    private UnitCrudService $unitService;

    public function __construct(UnitCrudService $unitService)
    {
        $this->unitService = $unitService;
    }

    public function index()
    {
        $data = $this->unitService->getUnitData();
        return response()->json([
            'status' => 'success',
            'message' => 'Units fetched successfully',
            'data' => $data
        ], 200);
    }
    public function userUnits($userId)
    {
        $data = $this->unitService->getUserUnits($userId);

        return response()->json([
            'status' => 'success',
            'message' => 'User units fetched successfully',
            'data' => $data
        ], 200);
    }

    public function vendorUnits($vendorId)
    {
        $data = $this->unitService->getVendorUnits($vendorId);

        return response()->json([
            'status' => 'success',
            'message' => 'Vendor units fetched successfully',
            'data' => $data
        ], 200);
    }


    public function store(UnitRequest $request)
    {
        $data = $this->unitService->createUnit($request->validated());
        return response()->json([
            'status' => 'success',
            'message' => 'Unit created successfully',
            'data' => $data
        ], 201);
    }

    public function show($id)
    {
        $data = $this->unitService->getUnitData($id);
        if (!$data) {
            return response()->json([
                'status' => 'error',
                'message' => 'Unit not found'
            ], 404);
        }

        return response()->json([
            'status' => 'success',
            'message' => 'Unit fetched successfully',
            'data' => $data
        ], 200);
    }

    public function update(UnitRequest $request, $id)
    {
        $updated = $this->unitService->updateUnitData($request->validated(), $id);
        if (!$updated) {
            return response()->json([
                'status' => 'error',
                'message' => 'Unit not found'
            ], 404);
        }

        return response()->json([
            'status' => 'success',
            'message' => 'Unit updated successfully'
        ], 200);
    }

    public function destroy($id)
    {
        $deleted = $this->unitService->deleteUnit($id);
        if (!$deleted) {
            return response()->json([
                'status' => 'error',
                'message' => 'Unit not found'
            ], 404);
        }

        return response()->json([
            'status' => 'success',
            'message' => 'Unit deleted successfully'
        ], 200);
    }
}

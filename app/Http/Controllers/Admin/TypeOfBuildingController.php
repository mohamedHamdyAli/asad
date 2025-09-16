<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\TypeOfBuildingRequest;
use App\services\TypeOfBuilding\TypeOfBuildingService;

class TypeOfBuildingController extends Controller
{
        private TypeOfBuildingService $typeOfBuildingService;

    public function __construct(TypeOfBuildingService $typeOfBuildingService)
    {
        $this->typeOfBuildingService = $typeOfBuildingService;
    }

    public function index()
    {
        $data = $this->typeOfBuildingService->getTypeOfBuildingData();
        return response()->json([
            'status' => 'success',
            'message' => 'Type of buildings fetched successfully',
            'data' => $data
        ], 200);
    }

    public function store(TypeOfBuildingRequest $request)
    {
        $this->typeOfBuildingService->createTypeOfBuilding($request->validated());
        return response()->json([
            'status' => 'success',
            'message' => 'Type of building created successfully',
        ], 201);
    }

    public function show($id)
    {
        $data = $this->typeOfBuildingService->getTypeOfBuildingData($id);
        if (!$data) {
            return response()->json([
                'status' => 'error',
                'message' => 'Type of building not found'
            ], 404);
        }

        return response()->json([
            'status' => 'success',
            'message' => 'Type of building fetched successfully',
            'data' => $data
        ], 200);
    }

    public function edit($id)
    {
        return $this->show($id);
    }

    public function update(TypeOfBuildingRequest $request, $id)
    {
        $updated = $this->typeOfBuildingService->updateTypeOfBuildingData($request->validated(), (int) $id);
        if (!$updated) {
            return response()->json([
                'status' => 'error',
                'message' => 'Type of building not found'
            ], 404);
        }

        return response()->json([
            'status' => 'success',
            'message' => 'Type of building updated successfully'
        ], 200);
    }

    public function destroy($id)
    {
        $deleted = $this->typeOfBuildingService->deleteTypeOfBuilding((int) $id);
        if (!$deleted) {
            return response()->json([
                'status' => 'error',
                'message' => 'Type of building not found'
            ], 404);
        }

        return response()->json([
            'status' => 'success',
            'message' => 'Type of building deleted successfully'
        ], 200);
    }
}

<?php

namespace App\Http\Controllers\Admin\Unit;

use App\Http\Controllers\Controller;
use App\services\Unit\UnitContractorCrudService;
use App\Http\Requests\Admin\UnitContractorRequest;

class UnitContractorController extends Controller
{
     private UnitContractorCrudService $unitContractorCrudService;


    public function __construct(UnitContractorCrudService $unitContractorCrudService)
    {
        $this->unitContractorCrudService = $unitContractorCrudService;
    }

    public function index($unitId)
    {
        $data = $this->unitContractorCrudService->getUnitContractors($unitId);
        return response()->json([
            'status' => 'success',
            'message' => 'Unit contractors fetched successfully',
            'data' => $data
        ], 200);
    }



    public function store(UnitContractorRequest $request)
    {
        $this->unitContractorCrudService->createUnitContractors($request->validated());
        return response()->json([
            'status' => 'success',
            'message' => 'Unit contractors created successfully',
        ], 201);
    }

    public function update(UnitContractorRequest $request)
    {
        $this->unitContractorCrudService->updateUnitContractors($request->validated());
        return response()->json([
            'status' => 'success',
            'message' => 'Unit contractors updated successfully',
        ], 200);
    }

    public function destroy($id)
    {
        $this->unitContractorCrudService->deleteUnitContractor($id);
        return response()->json([
            'status' => 'success',
            'message' => 'Unit contractor deleted successfully',
        ], 200);
    }
}

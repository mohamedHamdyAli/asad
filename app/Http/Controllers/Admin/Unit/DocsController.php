<?php

namespace App\Http\Controllers\Admin\Unit;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\UnitDocsRequest;
use App\Services\Unit\UnitDocsCrudService;

class DocsController extends Controller
{
    private UnitDocsCrudService $unitDocsService;

    public function __construct(UnitDocsCrudService $unitDocsService)
    {
        $this->unitDocsService = $unitDocsService;
    }

    public function index($unitId)
    {
        $data = $this->unitDocsService->getUnitDocsData($unitId);
        return response()->json([
            'status' => 'success',
            'message' => 'Units Doc fetched successfully',
            'data' => $data
        ], 200);
    }

    public function store(UnitDocsRequest $request)
    {
        $this->unitDocsService->createUnitDocs($request->validated());
        return response()->json([
            'status' => 'success',
            'message' => 'Unit Doc created successfully',
        ], 201);
    }


    public function update(UnitDocsRequest $request)
    {
        $this->unitDocsService->updateUnitDocsData($request->validated());
        return response()->json([
            'status' => 'success',
            'message' => 'Unit Doc updated successfully'
        ], 200);
    }

    public function destroy($id)
    {
        $this->unitDocsService->deleteUnitDocs($id);
        return response()->json([
            'status' => 'success',
            'message' => 'Unit Doc deleted successfully'
        ], 200);
    }
}

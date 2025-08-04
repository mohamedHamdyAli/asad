<?php

namespace App\Http\Controllers\Admin\Unit;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\UnitReportRequest;
use App\Services\Unit\UnitReportCrudService;

class ReportController extends Controller
{
    private UnitReportCrudService $unitReportService;

    public function __construct(UnitReportCrudService $unitReportService)
    {
        $this->unitReportService = $unitReportService;
    }

    public function index($unitId)
    {
        $data = $this->unitReportService->getUnitReportData($unitId);
        return response()->json([
            'status' => 'success',
            'message' => 'Unit Reports fetched successfully',
            'data' => $data
        ], 200);
    }

    public function store(UnitReportRequest $request)
    {
        $this->unitReportService->createUnitReports($request->validated());
        return response()->json([
            'status' => 'success',
            'message' => 'Unit Reports created successfully',
        ], 201);
    }

    public function update(UnitReportRequest $request)
    {
        $this->unitReportService->updateUnitReportData($request->validated());
        return response()->json([
            'status' => 'success',
            'message' => 'Unit Reports updated successfully'
        ], 200);
    }

    public function destroy($id)
    {
        $this->unitReportService->deleteUnitReport($id);
        return response()->json([
            'status' => 'success',
            'message' => 'Unit Report deleted successfully'
        ], 200);
    }
}

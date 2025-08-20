<?php

namespace App\Http\Controllers\Admin\Unit;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\UnitTimeLineRequest;
use App\services\Unit\UnitTimeLineCrudService;

class TimeLineController extends Controller
{
    private UnitTimeLineCrudService $unitTimeLineService;

    public function __construct(UnitTimeLineCrudService $unitTimeLineService)
    {
        $this->unitTimeLineService = $unitTimeLineService;
    }

    public function index($unitId)
    {
        $data = $this->unitTimeLineService->getUnitTimelineData($unitId);
        return response()->json([
            'status' => 'success',
            'message' => 'Unit Timelines fetched successfully',
            'data' => $data
        ], 200);
    }

    public function store(UnitTimeLineRequest $request)
    {
        $this->unitTimeLineService->createUnitTimelines($request->validated());
        return response()->json([
            'status' => 'success',
            'message' => 'Unit Timelines created successfully',
        ], 201);
    }

    public function update(UnitTimeLineRequest $request)
    {
        $this->unitTimeLineService->updateUnitTimelineData($request->validated());
        return response()->json([
            'status' => 'success',
            'message' => 'Unit Timelines updated successfully'
        ], 200);
    }

    public function destroy($id)
    {
        $this->unitTimeLineService->deleteUnitTimeline($id);
        return response()->json([
            'status' => 'success',
            'message' => 'Unit Timeline deleted successfully'
        ], 200);
    }
}

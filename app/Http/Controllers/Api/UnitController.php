<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\services\Unit\UnitApiService;
use App\Http\Requests\Api\UnitIdRequest;
use App\Http\Requests\Api\UserIdRequest;
use App\Http\Requests\Api\PhaseNoteRequest;
use App\Http\Resources\UnitDetailsResource;
use App\Http\Resources\FolderWithDocumentsResource;

class UnitController extends Controller
{
    private UnitApiService $unitService;
    public function __construct(UnitApiService $unitService)
    {
        $this->unitService = $unitService;
    }
    public function getUserUnit(UserIdRequest $request)
    {
        $data = $this->unitService->getUserUnit($request->validated());
        return successReturnData(UnitDetailsResource::collection($data ?? collect()), 'Data Fetched Successfully');
    }

    public function getUnitDetails(UnitIdRequest $request)
    {
        $data = $this->unitService->getUnitDetails($request->validated());
        return successReturnData(new UnitDetailsResource($data), 'Data Fetched Successfully');
    }

    public function getUnitDocs(UnitIdRequest $request)
    {
        return $this->unitService->getUnitDocs($request->validated());
    }

    public function getUnitGallery(UnitIdRequest $request)
    {
        return $this->unitService->getUnitGallery($request->validated());
    }

    public function getUnitDrawing(UnitIdRequest $request)
    {
        return $this->unitService->getUnitDrawing($request->validated());
    }
    public function getUnitReport(UnitIdRequest $request)
    {
        return $this->unitService->getUnitReport($request->validated());
    }
    public function getUnitPhase(UnitIdRequest $request)
    {
        return $this->unitService->getUnitPhase($request->validated());
    }
    public function storeUnitPhaseNote(PhaseNoteRequest $request)
    {
        return $this->unitService->storeUnitPhaseNote($request->validated());
    }
    public function getUnitTimeline(UnitIdRequest $request)
    {
        return $this->unitService->getUnitTimeline($request->validated());
    }
    public function getUnitContractors(UnitIdRequest $request)
    {
        return $this->unitService->getUnitContractors($request->validated());
    }
}

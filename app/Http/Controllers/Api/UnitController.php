<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\Api\UnitIdRequest;
use App\Http\Requests\Api\UserIdRequest;
use App\Http\Resources\FolderWithDocumentsResource;
use App\Http\Resources\UnitDetailsResource;
use App\services\Unit\UnitApiService;

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
}

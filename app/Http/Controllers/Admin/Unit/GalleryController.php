<?php

namespace App\Http\Controllers\Admin\Unit;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\UnitGalleryRequest;
use App\services\Unit\UnitGalleryCrudService;
use Illuminate\Http\Request;

class GalleryController extends Controller
{
    private UnitGalleryCrudService $unitGalleryService;

    public function __construct(UnitGalleryCrudService $unitGalleryService)
    {
        $this->unitGalleryService = $unitGalleryService;
    }

    public function index($unitId)
    {
        $data = $this->unitGalleryService->getUnitGalleryData($unitId);
        return response()->json([
            'status' => 'success',
            'message' => 'Units Gallery fetched successfully',
            'data' => $data
        ], 200);
    }

    public function store(UnitGalleryRequest $request)
    {
        $this->unitGalleryService->createUnitGallery($request->validated());
        return response()->json([
            'status' => 'success',
            'message' => 'Unit Gallery created successfully',
        ], 201);
    }


    public function update(UnitGalleryRequest $request)
    {
        $this->unitGalleryService->updateUnitGalleryData($request->validated());
        return response()->json([
            'status' => 'success',
            'message' => 'Unit Gallery updated successfully'
        ], 200);
    }

    public function destroy($id)
    {
        $this->unitGalleryService->deleteUnitGallery($id);
        return response()->json([
            'status' => 'success',
            'message' => 'Unit Gallery deleted successfully'
        ], 200);
    }
}

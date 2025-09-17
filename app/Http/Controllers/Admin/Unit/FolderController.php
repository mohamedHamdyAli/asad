<?php

namespace App\Http\Controllers\Admin\Unit;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\FolderRequest;
use App\services\Unit\FolderCrudService;
use Illuminate\Http\Request;

class FolderController extends Controller
{
    private FolderCrudService $folderService;
    public function __construct(FolderCrudService $folderService) { $this->folderService = $folderService; }

   public function index($type, Request $request)
{
    $unitId = $request->query('unit_id'); // unit_id comes from ?unit_id=123
    $data = $this->folderService->getData($type, $unitId);

    return response()->json([
        'status' => 'success',
        'message' => 'Folders fetched successfully',
        'data' => $data
    ], 200);
}

    public function store(FolderRequest $request)
    {
        $data = $this->folderService->createFolder($request->validated());
        return response()->json([
            'status' => 'success',
            'message' => 'Folder created successfully',
            'data' => $data
        ], 201);
    }

    public function update(FolderRequest $request, $id)
    {
        $data = $this->folderService->updateFolderData($request->validated(), (int)$id);
        return response()->json([
            'status' => 'success',
            'message' => 'Folder updated successfully',
            'data' => $data
        ], 200);
    }

    public function destroy($id)
    {
        $this->folderService->deleteFolder((int)$id);
        return response()->json([
            'status' => 'success',
            'message' => 'Folder deleted successfully'
        ], 200);
    }
}

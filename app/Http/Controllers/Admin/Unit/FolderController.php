<?php

namespace App\Http\Controllers\Admin\Unit;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\FolderRequest;
use App\Services\Unit\FolderCrudService;

class FolderController extends Controller
{
    private FolderCrudService $folderService;

    public function __construct(FolderCrudService $folderService)
    {
        $this->folderService = $folderService;
    }

    public function index($type)
    {
        $data = $this->folderService->getData($type);
        return response()->json([
            'status' => 'success',
            'message' => 'Folder fetched successfully',
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
        $data = $this->folderService->updateFolderData($request->validated(), $id);

        return response()->json([
            'status' => 'success',
            'message' => 'Folder updated successfully',
            'data' => $data
        ], 200);
    }

    public function destroy($id)
    {
         $this->folderService->deleteFolder($id);

        return response()->json([
            'status' => 'success',
            'message' => 'Folder deleted successfully'
        ], 200);
    }
}

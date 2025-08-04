<?php

namespace App\Services\Unit;

use App\Http\Resources\FolderWithDocumentsResource;
use App\Models\Unit;
use App\Services\FileService;
use Illuminate\Support\Facades\DB;

class UnitApiService
{
    public function getUserUnit($request)
    {
        return Unit::allUserUnit($request['user_id']);
    }

    public function getUnitDocs($request)
    {
        $docs = Unit::find($request['unit_id'])->unitDocument()->with('folder')->get();

        if ($docs->isEmpty()) {
            return failReturnMsg('No documents found for this unit', 404);
        }

        $data = $docs->groupBy(fn($doc) => optional($doc->folder)->name)->map(fn($items, $folderName) => [
            'foldername' => $folderName,
            'files' => $items,
        ])->values();

        return successReturnData(FolderWithDocumentsResource::collection($data), 'Data Fetched Successfully');
    }
    public function getUnitGallery($request)
    {
        $docs = Unit::find($request['unit_id'])->unitGallery()->with('folder')->get();

        if ($docs->isEmpty()) {
            return failReturnMsg('No Gallery found for this unit', 404);
        }

        $data = $docs->groupBy(fn($doc) => optional($doc->folder)->name)->map(fn($items, $folderName) => [
            'foldername' => $folderName,
            'files' => $items,
        ])->values();

        return successReturnData(FolderWithDocumentsResource::collection($data), 'Data Fetched Successfully');
    }


}

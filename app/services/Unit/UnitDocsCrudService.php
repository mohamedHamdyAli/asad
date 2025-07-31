<?php

namespace App\Services\Unit;

use App\Models\Unit;
use App\Models\UnitDocument;
use App\Services\FileService;
use Illuminate\Support\Facades\DB;

class UnitDocsCrudService
{
    private string $uploadFolder;

    public function __construct()
    {
        $this->uploadFolder = "units/docs";
    }

    public function getUnitDocsData($unitId)
    {
        return UnitDocument::where('unit_id', $unitId)->get();
    }

    public function createUnitDocs($request)
    {
        return DB::transaction(function () use ($request) {
            foreach ($request['data'] as $item) {
                $uploaded = FileService::upload($item['file'], $this->uploadFolder . '/' . getFolderName($item['folder_id']));
                UnitDocument::create([
                    'unit_id' => $request['unit_id'],
                    'folder_id' => $item['folder_id'],
                    'file' => $uploaded,
                    'title' => $item['title'],
                ]);
            }

        });
    }

    public function updateUnitDocsData($request)
    {
        return DB::transaction(function () use ($request) {
            foreach ($request['data'] as $item) {
                $doc = UnitDocument::findOrFail($item['id']);
                $updateData = [
                    'unit_id' => $request['unit_id'],
                ];

                if (array_key_exists('title', $item)) {
                    $updateData['title'] = $item['title'];
                }

                $folderId = array_key_exists('folder_id', $item) ? $item['folder_id'] : $doc->folder_id;
                $folderName = getFolderName($folderId);

                if (array_key_exists('folder_id', $item)) {
                    $updateData['folder_id'] = $item['folder_id'];
                }

                if (!empty($item['file'])) {
                    $updateData['file'] = FileService::upload($item['file'], $this->uploadFolder . '/' . $folderName);
                }

                $doc->update($updateData);
            }

            return true;
        });
    }






    public function deleteUnitDocs($id)
    {
        return DB::transaction(function () use ($id) {
            $UnitDocument = UnitDocument::findOrFail($id);
            FileService::delete($UnitDocument->getRawOriginal('file'));
            $UnitDocument->delete();
            return true;
        });
    }
}

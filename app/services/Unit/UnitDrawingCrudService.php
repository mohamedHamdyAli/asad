<?php

namespace App\Services\Unit;

use App\Models\UnitDrawing;
use App\Services\FileService;
use Illuminate\Support\Facades\DB;

class UnitDrawingCrudService
{
    private string $uploadFolder;

    public function __construct()
    {
        $this->uploadFolder = "units/drawings";
    }

    public function getUnitDrawingsData($unitId)
    {
        return UnitDrawing::where('unit_id', $unitId)->get();
    }

    public function createUnitDrawings($request)
    {
        return DB::transaction(function () use ($request) {
            foreach ($request['data'] as $item) {
                $uploaded = FileService::upload($item['image'], $this->uploadFolder . '/' . getFolderName($item['folder_id']));
                UnitDrawing::create([
                    'unit_id' => $request['unit_id'],
                    'folder_id' => $item['folder_id'],
                    'image' => $uploaded,
                    'title' => $item['title'],
                    'date' => $item['date'],
                ]);
            }
        });
    }

    public function updateUnitDrawingsData($request)
    {
        return DB::transaction(function () use ($request) {
            foreach ($request['data'] as $item) {
                $doc = UnitDrawing::findOrFail($item['id']);
                $updateData = [
                    'unit_id' => $request['unit_id'],
                ];

                if (array_key_exists('title', $item)) {
                    $updateData['title'] = $item['title'];
                }

                if (array_key_exists('date', $item)) {
                    $updateData['date'] = $item['date'];
                }

                $folderId = array_key_exists('folder_id', $item) ? $item['folder_id'] : $doc->folder_id;
                $folderName = getFolderName($folderId);

                if (array_key_exists('folder_id', $item)) {
                    $updateData['folder_id'] = $item['folder_id'];
                }

                if (!empty($item['image'])) {
                    $updateData['image'] = FileService::upload($item['image'], "{$this->uploadFolder}/$folderName");
                }

                $doc->update($updateData);
            }

            return true;
        });
    }

    public function deleteUnitDrawing($id)
    {
        return DB::transaction(function () use ($id) {
            $unitDrawing = UnitDrawing::findOrFail($id);
            FileService::delete($unitDrawing->getRawOriginal('image'));
            $unitDrawing->delete();
            return true;
        });
    }
}

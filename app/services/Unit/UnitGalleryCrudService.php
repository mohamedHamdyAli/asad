<?php

namespace App\Services\Unit;

use App\Models\UnitGallery;
use App\Services\FileService;
use Illuminate\Support\Facades\DB;

class UnitGalleryCrudService
{
    private string $uploadFolder;

    public function __construct()
    {
        $this->uploadFolder = "units/gallery";
    }

    public function getUnitGalleryData($unitId)
    {
        return UnitGallery::where('unit_id', $unitId)->get();
    }

    public function createUnitGallery($request)
    {
        return DB::transaction(function () use ($request) {
            foreach ($request['data'] as $item) {
                $uploaded = FileService::upload($item['image'], $this->uploadFolder . '/' . getFolderName($item['folder_id']));
                $title = !empty($item['title'])
                    ? json_encode($item['title'], JSON_UNESCAPED_UNICODE)
                    : null;
                UnitGallery::create([
                    'unit_id' => $request['unit_id'],
                    'folder_id' => $item['folder_id'],
                    'image' => $uploaded,
                    'title' => $title,
                    'date' => $item['date'],
                ]);
            }

        });
    }

    public function updateUnitGalleryData($request)
    {
        return DB::transaction(function () use ($request) {
            foreach ($request['data'] as $item) {
                $doc = UnitGallery::findOrFail($item['id']);
                $updateData = [
                    'unit_id' => $request['unit_id'],
                ];

                if (array_key_exists('title', $item) && is_array($item['title'])) {
                    $updateData['title'] = json_encode($item['title'], JSON_UNESCAPED_UNICODE);
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






    public function deleteUnitGallery($id)
    {
        return DB::transaction(function () use ($id) {
            $UnitGallery = UnitGallery::findOrFail($id);
            FileService::delete($UnitGallery->getRawOriginal('image'));
            $UnitGallery->delete();
            return true;
        });
    }
}

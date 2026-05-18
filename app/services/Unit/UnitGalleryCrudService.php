<?php

namespace App\services\Unit;

use App\Models\Unit;
use App\Models\UnitGallery;
use App\Models\User;
use App\services\FileService;
use App\Trait\notifications\NotifiesUnitOwnerTrait;
use Illuminate\Support\Facades\DB;

class UnitGalleryCrudService
{
    use NotifiesUnitOwnerTrait;

    private string $uploadFolder;

    public function __construct()
    {
        $this->uploadFolder = "units/gallery";
    }

    public function getUnitGalleryData($unitId)
    {
        return UnitGallery::where('unit_id', $unitId)->get();
    }

    public function createUnitGallery($request, ?User $actor = null)
    {
        $count = DB::transaction(function () use ($request) {
            $created = 0;
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
                $created++;
            }
            return $created;
        });

        if ($count > 0) {
            $unit = Unit::find($request['unit_id']);
            if ($unit) {
                $body = $count === 1
                    ? 'A new image was added to your project "{unit}".'
                    : "{$count} new images were added to your project \"{unit}\".";
                $this->notifyUnitOwner($unit, 'New gallery image', $body, $actor);
            }
        }
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

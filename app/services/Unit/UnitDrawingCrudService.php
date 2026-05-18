<?php

namespace App\services\Unit;

use App\Models\Unit;
use App\Models\UnitDrawing;
use App\Models\User;
use App\services\FileService;
use App\Trait\notifications\NotifiesUnitOwnerTrait;
use Illuminate\Support\Facades\DB;

class UnitDrawingCrudService
{
    use NotifiesUnitOwnerTrait;

    private string $uploadFolder;

    public function __construct()
    {
        $this->uploadFolder = "units/drawings";
    }

    public function getUnitDrawingsData($unitId)
    {
        return UnitDrawing::where('unit_id', $unitId)->get();
    }

    public function createUnitDrawings($request, ?User $actor = null)
    {
        $count = DB::transaction(function () use ($request) {
            $created = 0;
            foreach ($request['data'] as $item) {
                $uploaded = FileService::upload($item['image'], $this->uploadFolder . '/' . getFolderName($item['folder_id']));
                $title = !empty($item['title'])
                    ? json_encode($item['title'], JSON_UNESCAPED_UNICODE)
                    : null;
                UnitDrawing::create([
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
                    ? 'A new drawing was added to your project "{unit}".'
                    : "{$count} new drawings were added to your project \"{unit}\".";
                $this->notifyUnitOwner($unit, 'New drawing', $body, $actor);
            }
        }
    }

    public function updateUnitDrawingsData($request)
    {
        return DB::transaction(function () use ($request) {
            foreach ($request['data'] as $item) {
                $doc = UnitDrawing::findOrFail($item['id']);
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

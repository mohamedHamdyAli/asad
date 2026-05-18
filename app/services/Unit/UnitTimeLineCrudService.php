<?php

namespace App\Services\Unit;

use App\Models\Unit;
use App\Models\UnitTimeLine;
use App\Models\User;
use App\services\FileService;
use App\Trait\notifications\NotifiesUnitOwnerTrait;
use Illuminate\Support\Facades\DB;

class UnitTimeLineCrudService
{
    use NotifiesUnitOwnerTrait;

    private string $uploadFolder;

    public function __construct()
    {
        $this->uploadFolder = 'units/timelines';
    }

    public function getUnitTimelineData($unitId)
    {
        return UnitTimeLine::where('unit_id', $unitId)->get();
    }

    public function createUnitTimelines($request, ?User $actor = null)
    {
        $count = DB::transaction(function () use ($request) {
            $created = 0;
            foreach ($request['data'] as $item) {
                $uploaded = FileService::upload($item['file'], $this->uploadFolder);
                $title = !empty($item['title'])
                    ? json_encode($item['title'], JSON_UNESCAPED_UNICODE)
                    : null;

                UnitTimeLine::create([
                    'unit_id' => $request['unit_id'],
                    'file' => $uploaded,
                    'title' => $title,
                ]);
                $created++;
            }
            return $created;
        });

        if ($count > 0) {
            $unit = Unit::find($request['unit_id']);
            if ($unit) {
                $body = $count === 1
                    ? 'A new timeline entry was added to your project "{unit}".'
                    : "{$count} new timeline entries were added to your project \"{unit}\".";
                $this->notifyUnitOwner($unit, 'New timeline entry', $body, $actor);
            }
        }
    }

    public function updateUnitTimelineData($request)
    {
        return DB::transaction(function () use ($request) {
            foreach ($request['data'] as $item) {
                $doc = UnitTimeLine::findOrFail($item['id']);

                $updateData = [
                    'unit_id' => $request['unit_id'],
                ];

                if (array_key_exists('title', $item) && is_array($item['title'])) {
                    $updateData['title'] = json_encode($item['title'], JSON_UNESCAPED_UNICODE);
                }

                if (!empty($item['file'])) {
                    $updateData['file'] = FileService::upload($item['file'], $this->uploadFolder);
                }

                $doc->update($updateData);
            }

            return true;
        });
    }

    public function deleteUnitTimeline($id)
    {
        return DB::transaction(function () use ($id) {
            $unitTimeline = UnitTimeLine::findOrFail($id);
            FileService::delete($unitTimeline->getRawOriginal('file'));
            $unitTimeline->delete();
            return true;
        });
    }
}

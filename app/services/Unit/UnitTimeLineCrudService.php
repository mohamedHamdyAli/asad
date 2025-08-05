<?php

namespace App\Services\Unit;

use App\Models\UnitTimeLine;
use App\Services\FileService;
use Illuminate\Support\Facades\DB;

class UnitTimeLineCrudService
{
    private string $uploadFolder;

    public function __construct()
    {
        $this->uploadFolder = 'units/timelines';
    }

    public function getUnitTimelineData($unitId)
    {
        return UnitTimeLine::where('unit_id', $unitId)->get();
    }

    public function createUnitTimelines($request)
    {
        return DB::transaction(function () use ($request) {
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
            }
        });
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

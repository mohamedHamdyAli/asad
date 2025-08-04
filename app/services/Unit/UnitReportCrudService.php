<?php

namespace App\Services\Unit;

use App\Models\UnitReport;
use App\Services\FileService;
use Illuminate\Support\Facades\DB;

class UnitReportCrudService
{
    private string $uploadFolder;

    public function __construct()
    {
        $this->uploadFolder = "units/reports";
    }

    public function getUnitReportData($unitId)
    {
        return UnitReport::where('unit_id', $unitId)->get();
    }

    public function createUnitReports($request)
    {
        return DB::transaction(function () use ($request) {
            foreach ($request['data'] as $item) {
                $uploaded = FileService::upload($item['file'], $this->uploadFolder);
                $title = !empty($item['title'])
                    ? json_encode($item['title'], JSON_UNESCAPED_UNICODE)
                    : null;

                UnitReport::create([
                    'unit_id' => $request['unit_id'],
                    'file' => $uploaded,
                    'title' => $title,
                ]);
            }
        });
    }

    public function updateUnitReportData($request)
    {
        return DB::transaction(function () use ($request) {
            foreach ($request['data'] as $item) {
                $doc = UnitReport::findOrFail($item['id']);
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

    public function deleteUnitReport($id)
    {
        return DB::transaction(function () use ($id) {
            $unitReport = UnitReport::findOrFail($id);
            FileService::delete($unitReport->getRawOriginal('file'));
            $unitReport->delete();
            return true;
        });
    }
}

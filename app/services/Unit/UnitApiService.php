<?php

namespace App\services\Unit;

use App\Http\Resources\FolderWithDocumentsResource;
use App\Http\Resources\UnitDetailsResource;
use App\Http\Resources\UnitDocumentResource;
use App\Http\Resources\UnitPhaseResource;
use App\Models\Unit;

class UnitApiService
{
    public function getUserUnit($request)
    {
        return Unit::allUserUnit($request['user_id']);
    }
    public function getUnitDetails($request)
    {
        return Unit::find($request['unit_id']);
    }

    public function getUnitDocs($request)
    {
        $docs = Unit::find($request['unit_id'])->unitDocument()->with('folder')->get();

        if ($docs->isEmpty()) {
            return failReturnMsg('No documents found for this unit', 404);
        }

        $data = $docs->groupBy(fn($doc) => $doc->folder ? getLocalizedValue($doc->folder, 'name') : null)->map(fn($items, $folderName) => [
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

        $data = $docs->groupBy(fn($doc) => $doc->folder ? getLocalizedValue($doc->folder, 'name') : null)->map(fn($items, $folderName) => [
            'foldername' => $folderName,
            'files' => $items,
        ])->values();

        return successReturnData(FolderWithDocumentsResource::collection($data), 'Data Fetched Successfully');
    }

    public function getUnitDrawing($request)
    {
        $docs = Unit::find($request['unit_id'])->unitDrawing()->with('folder')->get();

        if ($docs->isEmpty()) {
            return failReturnMsg('No Drawing found for this unit', 404);
        }

        $data = $docs->groupBy(fn($doc) => $doc->folder ? getLocalizedValue($doc->folder, 'name') : null)->map(fn($items, $folderName) => [
            'foldername' => $folderName,
            'files' => $items,
        ])->values();

        return successReturnData(FolderWithDocumentsResource::collection($data), 'Data Fetched Successfully');
    }

    public function getUnitReport($request)
    {
        $report = Unit::find($request['unit_id'])->unitReport()->get();

        if ($report->isEmpty()) {
            return failReturnMsg('No report found for this unit', 404);
        }

        return successReturnData(UnitDocumentResource::collection($report), 'Data Fetched Successfully');
    }

    public function getUnitPhase($request)
{
    $unit = Unit::find($request['unit_id']);

    if (!$unit || $unit->unitPhase->isEmpty()) {
        return failReturnMsg('No phase found for this unit', 404);
    }

    $phases = $unit->unitPhase;

    $oldStatuses = $phases->pluck('status')->slice(0, -1)->implode(',');

    $lastPhase = $phases->last();
    $response = [
        'old_status'    => $oldStatuses,
        'active_status' => $lastPhase->status,
        'desc'          => getLocalizedValue($lastPhase, 'description')
    ];

    return successReturnData($response, 'Data Fetched Successfully');
}



}

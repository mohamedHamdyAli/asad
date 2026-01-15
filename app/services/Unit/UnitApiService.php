<?php

namespace App\services\Unit;

use App\Models\Unit;
use App\Models\UnitIssue;
use App\Models\UnitPhaseNote;
use App\Http\Resources\UnitNoteResource;
use App\Http\Resources\UnitIssueResource;
use App\Http\Resources\ConsultantResource;
use App\Http\Resources\ContractorResource;
use App\Http\Resources\UnitDocumentResource;
use App\Http\Resources\FolderWithDocumentsResource;

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

        $data = $docs->groupBy(fn($doc) => $doc->folder ? getLocalizedValue($doc->folder, 'name') : null)
            ->map(function ($items, $folderName) {
                $folder = $items->first()->folder;
                return [
                    'foldername' => $folderName,
                    'folder_image' => $folder?->folder_image,
                    'files' => $items,
                ];
            })->values();

        return successReturnData(FolderWithDocumentsResource::collection($data), 'Data Fetched Successfully');
    }
    public function getUnitGallery($request)
    {
        $docs = Unit::find($request['unit_id'])->unitGallery()->with('folder')->get();

        if ($docs->isEmpty()) {
            return failReturnMsg('No Gallery found for this unit', 404);
        }

        $data = $docs->groupBy(fn($doc) => $doc->folder ? getLocalizedValue($doc->folder, 'name') : null)
            ->map(function ($items, $folderName) {
                $folder = $items->first()->folder;
                return [
                    'foldername' => $folderName,
                    'folder_image' => $folder?->folder_image,
                    'files' => $items,
                ];
            })->values();

        return successReturnData(FolderWithDocumentsResource::collection($data), 'Data Fetched Successfully');
    }

    public function getUnitDrawing($request)
    {
        $docs = Unit::find($request['unit_id'])->unitDrawing()->with('folder')->get();

        if ($docs->isEmpty()) {
            return failReturnMsg('No Drawing found for this unit', 404);
        }

        $data = $docs->groupBy(fn($doc) => $doc->folder ? getLocalizedValue($doc->folder, 'name') : null)
            ->map(function ($items, $folderName) {
                $folder = $items->first()->folder;
                return [
                    'foldername' => $folderName,
                    'folder_image' => $folder?->folder_image,
                    'files' => $items,
                ];
            })->values();

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

        // $oldStatuses = $phases->pluck('status')->slice(0, -1)->implode(',');

        $lastPhase = $phases->last();
        $response = [
            // 'old_status' => $oldStatuses,
            'active_status' => $lastPhase->status_code,
            'desc' => getLocalizedValue($lastPhase, 'description')
        ];

        return successReturnData($response, 'Data Fetched Successfully');
    }

    public function storeUnitPhaseNote($request)
    {
        $user = userOrGuestAuth();
        $unit = Unit::find($request['unit_id']);
        if (!$unit) {
            return failReturnMsg('Unit not found');
        }

        $lastPhase = $unit->unitPhase()->latest('id')->first();

        if (!$lastPhase) {
            return failReturnMsg('No phase found for this unit', 404);
        }

        $note = UnitPhaseNote::create([
            'unit_id' => $unit->id,
            'unit_phase_id' => $lastPhase->id,
            'user_id' => $user->id,
            'note' => $request['note'],
        ]);

        $response = [
            'user_id' => $user->id,
            'unit_id' => $unit->id,
            'note' => $note->note,
        ];
        return successReturnData($response, 'Note saved successfully');
    }


    public function getUnitTimeline($request)
    {
        $timeline = Unit::find($request['unit_id'])->unitTimeLine()->get();

        if ($timeline->isEmpty()) {
            return failReturnMsg('No timeline found for this unit', 404);
        }

        return successReturnData(UnitDocumentResource::collection($timeline), 'Data Fetched Successfully');
    }

    public function getUnitContractors($request)
    {
        $unit = Unit::find($request['unit_id']);

        if (!$unit) {
            return failReturnMsg('Unit not found', 404);
        }

        $unitContractors = $unit->unitContractor()->with('contractor')->get();

        $contractors = $unitContractors->pluck('contractor')->filter();

        if ($contractors->isEmpty()) {
            return failReturnMsg('No contractors found for this unit', 404);
        }

        return successReturnData(ContractorResource::collection($contractors), 'Data Fetched Successfully');
    }

    public function getUnitConsultants($request)
    {
        $unit = Unit::find($request['unit_id']);

        if (!$unit) {
            return failReturnMsg('Unit not found', 404);
        }

        $unitConsultants = $unit->unitConstulant()->with('consultant')->get();

        $consultants = $unitConsultants->pluck('consultant')->filter();

        if ($consultants->isEmpty()) {
            return failReturnMsg('No consultants found for this unit', 404);
        }

        return successReturnData(ConsultantResource::collection($consultants), 'Data Fetched Successfully');
    }


    public function reportUnitIssue($request)
    {
        $user = userOrGuestAuth();
        $unit = Unit::find($request['unit_id']);

        if (!$unit) {
            return failReturnMsg('Unit not found');
        }

        $issue = UnitIssue::create([
            'user_id' => $user->id,
            'unit_id' => $unit->id,
            'title' => $request['title'],
            'description' => $request['description'],
        ]);

        return  returnSuccessMsg('Issue reported successfully');
    }


 public function getUnitData($request, $type)
{
    $user = userAuth();

    if (! $user) {
        return failReturnMsg('Unauthenticated', 401);
    }

    if (!in_array($type, ['issues', 'notes'])) {
        return failReturnMsg('Invalid type. Allowed: issues, notes', 422);
    }

    if ($type === 'notes') {
        $notes = UnitPhaseNote::where('user_id', $user->id)
            ->with(['user:id,name'])
            ->get();

        return successReturnData([
            'type'  => 'notes',
            'items' => UnitNoteResource::collection($notes),
        ], 'Notes fetched successfully');
    }

    if ($type === 'issues') {
        $issues = UnitIssue::where('user_id', $user->id)
            ->with(['user:id,name'])
            ->get();

        return successReturnData([
            'type'  => 'issues',
            'items' => UnitIssueResource::collection($issues),
        ], 'Issues fetched successfully');
    }
}

}

<?php

namespace App\services\Unit;

use App\Models\UnitPhase;
use Illuminate\Support\Facades\DB;

class UnitPhaseCrudService
{
    public function getUnitPhases($unitId)
    {
        return UnitPhase::where('unit_id', $unitId)->get();
    }
    public function createPhases($request)
    {
        return DB::transaction(function () use ($request) {
            foreach ($request['data'] as $item) {
                $description = !empty($item['description']) && is_array($item['description'])
                    ? json_encode($item['description'], JSON_UNESCAPED_UNICODE)
                    : null;

                UnitPhase::updateOrCreate(
                    [
                        'unit_id' => $request['unit_id'],
                        'status' => $item['status'],
                    ],
                    [
                        'description' => $description,
                    ]
                );
            }
        });
    }

    public function updatePhases($request)
    {
        return DB::transaction(function () use ($request) {
            foreach ($request['data'] as $item) {
                $phase = UnitPhase::findOrFail($item['id']);

                $updateData = [];

                if (isset($item['status'])) {
                    $updateData['status'] = $item['status'];
                }

                if (!empty($item['description']) && is_array($item['description'])) {
                    $updateData['description'] = json_encode($item['description'], JSON_UNESCAPED_UNICODE);
                }

                $phase->update($updateData);
            }

            return true;
        });
    }
    public function deletePhase($id)
    {
        return DB::transaction(function () use ($id) {
            $phase = UnitPhase::findOrFail($id);
            $phase->delete();
            return true;
        });
    }
}

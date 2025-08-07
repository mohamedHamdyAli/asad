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
                UnitPhase::create([
                    'unit_id' => $request['unit_id'],
                    'status' => $item['status'],
                    'description' => $item['description'],
                ]);
            }
        });
    }

    public function updatePhases($request)
    {
        return DB::transaction(function () use ($request) {
            foreach ($request['data'] as $item) {
                $phase = UnitPhase::findOrFail($item['id']);
                $phase->update([
                    'status' => $item['status'] ?? $phase->status,
                    'description' => $item['description'] ?? $phase->description,
                ]);
            }
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

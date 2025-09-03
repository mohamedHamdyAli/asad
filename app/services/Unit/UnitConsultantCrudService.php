<?php

namespace App\Services\Unit;

use App\Models\UnitConstulant;
use Illuminate\Support\Facades\DB;

class UnitConsultantCrudService
{
    public function getUnitConsultants($unitId)
    {
        return UnitConstulant::with('consultant')->where('unit_id', $unitId)->get();
    }

    public function createUnitConsultants($request)
    {
        return DB::transaction(function () use ($request) {
            foreach ($request['data'] as $item) {
                UnitConstulant::create([
                    'unit_id' => $request['unit_id'],
                    'consultant_id' => $item['consultant_id'],
                ]);
            }
        });
    }

    public function updateUnitConsultants($request)
    {
        return DB::transaction(function () use ($request) {
            foreach ($request['data'] as $item) {
                $record = UnitConstulant::findOrFail($item['id']);

                $updateData = [];

                if (isset($request['unit_id'])) {
                    $updateData['unit_id'] = $request['unit_id'];
                }

                if (isset($item['consultant_id'])) {
                    $updateData['consultant_id'] = $item['consultant_id'];
                }

                if (!empty($updateData)) {
                    $record->update($updateData);
                }
            }

            return true;
        });
    }

    public function deleteUnitConsultant($id)
    {
        return DB::transaction(function () use ($id) {
            $record = UnitConstulant::findOrFail($id);
            $record->delete();
            return true;
        });
    }
}

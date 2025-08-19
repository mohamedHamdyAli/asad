<?php

namespace App\services\Unit;

use App\Models\UnitContractor;
use Illuminate\Support\Facades\DB;

class UnitContractorCrudService
{
   public function getUnitContractors($unitId)
    {
        return UnitContractor::with('contractor')->where('unit_id', $unitId)->get();
    }

    public function createUnitContractors($request)
    {
        return DB::transaction(function () use ($request) {
            foreach ($request['data'] as $item) {
                UnitContractor::create([
                    'unit_id' => $request['unit_id'],
                    'contractor_id' => $item['contractor_id'],
                ]);
            }
        });
    }

    public function updateUnitContractors($request)
    {
        return DB::transaction(function () use ($request) {
            foreach ($request['data'] as $item) {
                $record = UnitContractor::findOrFail($item['id']);

                $updateData = [
                    'unit_id' => $request['unit_id'],
                    'contractor_id' => $item['contractor_id'],
                ];

                $record->update($updateData);
            }

            return true;
        });
    }

    public function deleteUnitContractor($id)
    {
        return DB::transaction(function () use ($id) {
            $record = UnitContractor::findOrFail($id);
            $record->delete();
            return true;
        });
    }
}

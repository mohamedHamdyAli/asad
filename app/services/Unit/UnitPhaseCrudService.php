<?php

namespace App\services\Unit;

use App\Models\Unit;
use App\Models\UnitPhase;
use App\Models\User;
use App\Trait\notifications\NotifiesUnitOwnerTrait;
use Illuminate\Support\Facades\DB;

class UnitPhaseCrudService
{
    use NotifiesUnitOwnerTrait;

    public function getUnitPhases($unitId)
    {
        return UnitPhase::where('unit_id', $unitId)->get();
    }

    public function createPhases($request, ?User $actor = null)
    {
        $created = DB::transaction(function () use ($request) {
            $names = [];
            foreach ($request['data'] as $item) {

                $status = trim($item['status']);

                $exists = UnitPhase::where('unit_id', $request['unit_id'])
                    ->where('status', $status)
                    ->exists();

                if ($exists) {
                    abort(409, "Phase '{$status}' already exists for this unit.");
                }

                UnitPhase::create([
                    'unit_id' => $request['unit_id'],
                    'status' => $status,
                    'description' => json_encode(
                        $item['description'],
                        JSON_UNESCAPED_UNICODE
                    ),
                ]);
                $names[] = $status;
            }

            return $names;
        });

        if (!empty($created)) {
            $unit = Unit::find($request['unit_id']);
            if ($unit) {
                $count = count($created);
                $body = $count === 1
                    ? "Phase \"{$created[0]}\" was added to your project \"{unit}\"."
                    : "{$count} new phases were added to your project \"{unit}\": " . implode(', ', $created) . '.';
                $this->notifyUnitOwner($unit, 'New project phase', $body, $actor);
            }
        }

        return true;
    }


    public function updatePhases($request)
    {
        return DB::transaction(function () use ($request) {

            foreach ($request['data'] as $item) {

                $phase = UnitPhase::findOrFail($item['id']);

                $updateData = [];

                if (isset($item['status'])) {

                    $status = trim($item['status']);

                    $exists = UnitPhase::where('unit_id', $phase->unit_id)
                        ->where('status', $status)
                        ->where('id', '!=', $phase->id)
                        ->exists();

                    if ($exists) {
                        abort(
                            409,
                            "Phase '{$status}' already exists for this unit."
                        );
                    }

                    $updateData['status'] = $status;
                }

                if (!empty($item['description'])) {
                    $updateData['description'] = json_encode(
                        $item['description'],
                        JSON_UNESCAPED_UNICODE
                    );
                }

                if (!empty($updateData)) {
                    $phase->update($updateData);
                }
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

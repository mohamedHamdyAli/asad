<?php

namespace App\services\Unit;

use App\Models\UnitLiveCamera;
use Illuminate\Support\Facades\DB;

class UnitLiveCameraCrudService
{
    public function getUnitLiveCameras($unitId)
    {
        return UnitLiveCamera::where('unit_id', $unitId)->get();
    }

    public function createUnitLiveCameras($request)
    {
        return DB::transaction(function () use ($request) {
            foreach ($request['data'] as $item) {
                UnitLiveCamera::create([
                    'unit_id' => $request['unit_id'],
                    'ip_address' => $item['ip_address'],
                    'camera_link' => $item['camera_link'] ?? null,
                ]);
            }
        });
    }

    public function updateUnitLiveCameras($request)
    {
        return DB::transaction(function () use ($request) {
            foreach ($request['data'] as $item) {
                $camera = UnitLiveCamera::findOrFail($item['id']);

                $camera->update([
                    'unit_id' => $request['unit_id'] ,
                    'ip_address' => $item['ip_address'] ?? $camera->ip_address,
                    'camera_link' => $item['camera_link'] ?? $camera->camera_link,
                ]);
            }

            return true;
        });
    }

    public function deleteUnitLiveCamera($id)
    {
        return DB::transaction(function () use ($id) {
            $camera = UnitLiveCamera::findOrFail($id);
            $camera->delete();
            return true;
        });
    }
}

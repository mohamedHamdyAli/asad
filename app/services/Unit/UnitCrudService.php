<?php

namespace App\Services\Unit;

use App\Models\Unit;
use App\Services\FileService;
use Illuminate\Support\Facades\DB;

class UnitCrudService
{
    private string $uploadFolder;

    public function __construct()
    {
        $this->uploadFolder = "units";
    }

    public function getUnitData($id = null)
    {
        return $id ? Unit::find($id) : Unit::all();
    }
    public function getUserUnits($userId, $status = null)
    {
        return Unit::allUserUnit($userId, $status);
    }

    public function getVendorUnits($vendorId, $status = null)
    {
        return Unit::allVendorUnit($vendorId, $status);
    }

    public function createUnit($request)
    {
        return DB::transaction(function () use ($request) {
            if (!empty($request['cover_image'])) {
                $request['cover_image'] = FileService::upload($request['cover_image'], $this->uploadFolder);
            }

            if (!empty($request['name'])) {
                $request['name'] = json_encode($request['name'], JSON_UNESCAPED_UNICODE);
            }
            if (!empty($request['description'])) {
                $request['description'] = json_encode($request['description'], JSON_UNESCAPED_UNICODE);
            }

            $unit = Unit::create($request);
            if (!empty($request['gallery']) && is_array($request['gallery'])) {
                foreach ($request['gallery'] as $image) {
                    $unit->homeUnitGallery()->create([
                        'image' => FileService::upload($image, "{$this->uploadFolder}/gallery"),
                    ]);
                }
            }
            return $unit;
        });
    }

    public function updateUnitData($request, $id)
    {
        return DB::transaction(function () use ($request, $id) {
            $unit = Unit::find($id);
            if (!$unit) return false;

            if (!empty($request['cover_image'])) {
                $request['cover_image'] = FileService::replace(
                    $request['cover_image'],
                    $this->uploadFolder,
                    $unit->getRawOriginal('cover_image')
                );
            }
            if (!empty($request['name'])) {
                $request['name'] = json_encode($request['name'], JSON_UNESCAPED_UNICODE);
            }
            if (!empty($request['description'])) {
                $request['description'] = json_encode($request['description'], JSON_UNESCAPED_UNICODE);
            }

            $unit->update($request);

            if (!empty($request['gallery']) && is_array($request['gallery'])) {
                foreach ($request['gallery'] as $image) {
                    $unit->homeUnitGallery()->create([
                        'image' => FileService::upload($image, "{$this->uploadFolder}/gallery"),
                    ]);
                }
            }

            return true;
        });
    }


    public function deleteUnit($id)
    {
        return DB::transaction(function () use ($id) {
            $unit = Unit::find($id);
            if (!$unit)
                return false;

            FileService::delete($unit->getRawOriginal('cover_image'));
            $unit->delete();
            return true;
        });
    }
}

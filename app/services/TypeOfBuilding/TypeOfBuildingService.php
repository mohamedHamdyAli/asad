<?php

namespace App\services\TypeOfBuilding;

use App\Models\TypeOfBuilding;
use Illuminate\Support\Facades\DB;

class TypeOfBuildingService
{
    public function getTypeOfBuildingData($id = null)
    {
        return $id ? TypeOfBuilding::find($id) : TypeOfBuilding::all();
    }

public function createTypeOfBuilding($request): bool
{
    return DB::transaction(function () use ($request) {
        $items = $request['data'] ?? [];

        foreach ($items as $item) {
            $title = json_encode($item['title'], JSON_UNESCAPED_UNICODE);
            TypeOfBuilding::create([
                'title' => $title,
                'price' => $item['price'] ?? null,
            ]);
        }

        return true;
    });
}

    public function updateTypeOfBuildingData(array $request, int $id): bool
    {
        return DB::transaction(function () use ($request, $id) {
            if (isset($request['data']) && is_array($request['data'])) {
                $request = $request['data'];
            }

            $record = TypeOfBuilding::find($id);
            if (!$record) return false;

            if (!empty($request['title']) && is_array($request['title'])) {
                $request['title'] = json_encode($request['title'], JSON_UNESCAPED_UNICODE);
            }

            $record->update($request);
            return true;
        });
    }

    public function deleteTypeOfBuilding(int $id): bool
    {
        return DB::transaction(function () use ($id) {
            $record = TypeOfBuilding::find($id);
            if (!$record) return false;
            $record->delete();
            return true;
        });
    }
}

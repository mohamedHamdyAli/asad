<?php

namespace App\services\TypeOfPrice;

use App\Models\TypeOfPrice;
use Illuminate\Support\Facades\DB;

class TypeOfPriceService
{
    public function getTypeOfPriceData($id = null)
    {
        return $id ? TypeOfPrice::find($id) : TypeOfPrice::all();
    }

    public function createTypeOfPrice($request): bool
    {
        return DB::transaction(function () use ($request) {
            $items = $request['data'] ?? [];

            foreach ($items as $item) {
                $title = json_encode($item['title'], JSON_UNESCAPED_UNICODE);
                TypeOfPrice::create([
                    'title' => $title,
                ]);
            }

            return true;
        });
    }

    public function updateTypeOfPriceData(array $request, int $id): bool
    {
        return DB::transaction(function () use ($request, $id) {
            if (isset($request['data']) && is_array($request['data'])) {
                $request = $request['data'];
            }

            $record = TypeOfPrice::find($id);
            if (!$record) return false;

            if (!empty($request['title']) && is_array($request['title'])) {
                $request['title'] = json_encode($request['title'], JSON_UNESCAPED_UNICODE);
            }

            $record->update($request);
            return true;
        });
    }

    public function deleteTypeOfPrice(int $id): bool
    {
        return DB::transaction(function () use ($id) {
            $record = TypeOfPrice::find($id);
            if (!$record) return false;
            $record->delete();
            return true;
        });
    }
}

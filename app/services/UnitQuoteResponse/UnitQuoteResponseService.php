<?php

namespace App\Services\UnitQuoteResponse;

use App\Models\UnitQuoteResponse;
use Illuminate\Support\Facades\DB;

class UnitQuoteResponseService
{
    public function getAll()
    {
        return UnitQuoteResponse::select([
                'id',
                'unit_quote_id',
                'vendor_id',
                'user_id',
                'title',
                'price',
                'time_line',
                'created_at',
                'updated_at'
            ])
            ->get();
    }

    public function getById(int $id)
    {
        return UnitQuoteResponse::select([
                'id',
                'unit_quote_id',
                'vendor_id',
                'user_id',
                'title',
                'price',
                'time_line',
                'created_at',
                'updated_at'
            ])
            ->find($id);
    }

    public function create(array $validated)
{
    return DB::transaction(function () use ($validated) {
        $data = $validated['data'];

        $data['title'] = json_encode($data['title'], JSON_UNESCAPED_UNICODE);
        $data['time_line'] = isset($data['time_line'])
            ? json_encode($data['time_line'], JSON_UNESCAPED_UNICODE)
            : null;

        return UnitQuoteResponse::create($data);
    });
}

public function update(UnitQuoteResponse $response, array $validated)
{
    return DB::transaction(function () use ($response, $validated) {
        $data = $validated['data'];

        if (isset($data['title'])) {
            $data['title'] = json_encode($data['title'], JSON_UNESCAPED_UNICODE);
        }

        if (isset($data['time_line'])) {
            $data['time_line'] = json_encode($data['time_line'], JSON_UNESCAPED_UNICODE);
        }

        $response->update($data);
        return $response;
    });
}


    public function delete(UnitQuoteResponse $response)
    {
        return DB::transaction(function () use ($response) {
            return $response->delete();
        });
    }
}

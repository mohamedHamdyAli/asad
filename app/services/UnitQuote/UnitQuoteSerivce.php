<?php

namespace App\services\UnitQuote;

use App\Models\UnitQuote;
use App\services\FileService;
use Illuminate\Support\Facades\DB;

class UnitQuoteSerivce
{
    private string $uploadFolder;

    public function __construct()
    {
        $this->uploadFolder = "unit_quotes";
    }

    public function getAll()
    {
        return UnitQuote::select([
            'id',
            'title',
            'other_title',
            'user_id',
            'type_of_building_id',
            'type_of_price_id',
            'pay_image',
            'created_at',
            'updated_at'
        ])->with('unitQuoteGallery')
            ->get();
    }

    public function getById(int $id)
    {
        return UnitQuote::select([
            'id',
            'title',
            'other_title',
            'user_id',
            'type_of_building_id',
            'type_of_price_id',
            'pay_image',
            'created_at',
            'updated_at'
        ])->with('unitQuoteGallery')
            ->find($id);
    }

    public function create(array $data)
    {
        return DB::transaction(function () use ($data) {
            if (isset($data['pay_image'])) {
                $data['pay_image'] = FileService::upload($data['pay_image'], $this->uploadFolder);
            }

            $unitQuote = UnitQuote::create($data);

            if (isset($data['gallery']) && is_array($data['gallery'])) {
                foreach ($data['gallery'] as $image) {
                    $unitQuote->unitQuoteGallery()->create([
                        'image' => FileService::upload($image, $this->uploadFolder)
                    ]);
                }
            }

            return $unitQuote;
        });
    }

    public function update(UnitQuote $unitQuote, array $data)
    {
        return DB::transaction(function () use ($unitQuote, $data) {
            if (isset($data['pay_image'])) {
                $data['pay_image'] = FileService::replace(
                    $data['pay_image'],
                    $this->uploadFolder,
                    $unitQuote->getRawOriginal('pay_image')
                );
            }

            $unitQuote->update($data);

            return $unitQuote;
        });
    }

    public function delete(UnitQuote $unitQuote)
    {
        return DB::transaction(function () use ($unitQuote) {
            if ($unitQuote->pay_image) {
                FileService::delete($unitQuote->getRawOriginal('pay_image'));
            }

            foreach ($unitQuote->unitQuoteGallery as $img) {
                FileService::delete($img->getRawOriginal('image'));
                $img->delete();
            }

            return $unitQuote->delete();
        });
    }
}

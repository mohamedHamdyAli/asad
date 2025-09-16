<?php

namespace App\services\Quote;

use App\Http\Resources\PriceResponseResource;
use App\Http\Resources\TypeBuildingResource;
use App\Http\Resources\TypePriceResource;
use App\Models\TypeOfBuilding;
use App\Models\TypeOfPrice;
use App\Models\UnitQuote;
use App\Models\UnitQuoteResponse;
use App\services\FileService;
use Illuminate\Support\Facades\DB;

class QuoteApiService
{
    public function getBuildingType()
    {
        return successReturnData(TypeBuildingResource::collection(TypeOfBuilding::allData()), 'Data Fetched Successfully');
    }
    public function getPriceType()
    {
        return successReturnData(TypePriceResource::collection(TypeOfPrice::allData()), 'Data Fetched Successfully');
    }
    public function quoteRequest($request)
    {
        return DB::transaction(function () use ($request) {

            if (!empty($request['pay_image'])) {
                $request['pay_image'] = FileService::upload($request['pay_image'], 'UnitQuote/pay_image');
            }

            $unitQuote = UnitQuote::create($request);

            if (!empty($request['images']) && is_array($request['images'])) {
                foreach ($request['images'] as $image) {
                    $imagePath = FileService::upload($image, 'UnitQuote/gallery');
                    $unitQuote->unitQuoteGallery()->create(['image' => $imagePath]);
                }
            }
            return returnSuccessMsg('Data added Successfully');
        });
    }
    public function priceResponse($request)
    {
        $data = UnitQuoteResponse::allUserData($request['user_id']);
        if ($data->isEmpty()) {
            return failReturnMsg('No Data Found', 404);
        }
        return successReturnData(PriceResponseResource::collection($data), 'Data Fetched Successfully');
    }
}

<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Resources\BannerResource;
use App\Services\Banner\BannerHelperFunctionService;
use function successReturnData;

class BannerController extends Controller
{
    private BannerHelperFunctionService $bannerService;
    public function __construct(BannerHelperFunctionService $bannerService)
    {
        $this->bannerService = $bannerService;
    }
    public function getBanner()
    {
        $data = $this->bannerService->getAllActiveBanners();
        return successReturnData(BannerResource::collection($data), 'Data Fetched Successfully');

    }
}

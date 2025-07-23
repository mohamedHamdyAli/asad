<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\BannerRequest;
use App\Services\Banner\BannerHelperFunctionService;
use App\Models\Banner;

class BannerController extends Controller
{
    protected $bannerService;

    public function __construct(BannerHelperFunctionService $bannerService)
    {
        $this->bannerService = $bannerService;
    }

    public function index()
    {
        $banners = $this->bannerService->getAllBanners();
        return response()->json([
            'status' => 'success',
            'message' => $banners,
        ], 200);
    }

    public function store(BannerRequest $request)
    {
        $banner = $this->bannerService->createBanner($request->validated());


        return response()->json([
            'status' => 'success',
            'message' => 'Banner created successfully',
        ], 200);
    }


    public function show($id)
    {
        $banner = $this->bannerService->getBannerById($id);

        if (!$banner) {
            return response()->json([
                'status' => 'error',
                'message' => 'Banner not found'
            ]);
        }

        return response()->json([
            'status' => 'success',
            'message' => 'Banner fetched successfully',
            'data' => $banner
        ], 200);

    }

    public function edit($id)
    {
        $banner = $this->bannerService->getBannerById($id);

        return response()->json([
            'status' => 'success',
            'message' => 'Banner edit successfully',
            'data' => $banner
        ], 200);

    }


    public function update(BannerRequest $request, $id)
    {
        $banner = $this->bannerService->getBannerById($id);
        if (!$banner) {
            return response()->json([
                'status' => 'error',
                'message' => 'Banner not found'
            ], 200);
        }

        $updatedBanner = $this->bannerService->updateBanner($banner, $request->validated());
        return response()->json([
            'status' => 'success',
            'message' => 'Banner Updated successfully',
        ], 200);
    }


    public function destroy($id)
    {
        $banner = $this->bannerService->getBannerById($id);
        if (!$banner) {
            return response()->json([
                'status' => 'error',
                'message' => 'Banner not found'
            ], 200);
        }

        $this->bannerService->deleteBanner($banner);
        return response()->json([
            'status' => 'success',
            'message' => 'Banner Deleted successfully'
        ], 200);
    }
}

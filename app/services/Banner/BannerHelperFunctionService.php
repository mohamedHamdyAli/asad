<?php

namespace App\services\Banner;

use App\Models\Banner;
use App\services\FileService;
use Illuminate\Support\Facades\DB;

class BannerHelperFunctionService
{
    private string $uploadFolder;

    public function __construct()
    {
        $this->uploadFolder = "banners";
    }

    public function getAllBanners()
    {
        return Banner::allActive();
    }

    public function getAllActiveBanners()
    {
        return Banner::allActive(true);
    }

    public function getBannerById(int $id)
    {
        return Banner::find($id);
    }

    public function createBanner(array $data)
    {
        return DB::transaction(function () use ($data) {
            $bannerData = $data;

            if (isset($bannerData['image'])) {
                $bannerData['image'] = FileService::upload($bannerData['image'], $this->uploadFolder);
            }
            return Banner::create($bannerData);
        });
    }


    public function updateBanner(Banner $banner, array $data)
    {
        return DB::transaction(function () use ($banner, $data) {
            $bannerData = $data;

            if (isset($bannerData['image'])) {
                $bannerData['image'] = FileService::replace(
                    $bannerData['image'],
                    $this->uploadFolder,
                    $banner->getRawOriginal('image')
                );
            }

            $banner->update($bannerData);
            return $banner;
        });
    }

    public function deleteBanner(Banner $banner)
    {
        return DB::transaction(function () use ($banner) {
            if ($banner->image) {
                FileService::delete($banner->getRawOriginal('image'));
            }
            return $banner->delete();
        });
    }
}

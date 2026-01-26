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

    public function getAllActiveBanners($bannerType = 'guest')
    {
        return Banner::where('page', 'application')
            ->where('is_enabled', 1)
            ->where('banner_type', $bannerType)
            ->get();
    }

    public function getBannerById(int $id)
    {
        return Banner::find($id);
    }
    public function getBannerData($id = null)
    {
        return $id ? Banner::find($id) : Banner::allActive();
    }
    public function createBanner(array $data)
    {
        return DB::transaction(function () use ($data) {
            $bannerData = $data;

            if (isset($bannerData['image'])) {
                $bannerData['image'] = FileService::upload($bannerData['image'], $this->uploadFolder);
            }
            $bannerData['name'] = json_encode($bannerData['name'], JSON_UNESCAPED_UNICODE);
            $bannerData['description'] = json_encode($bannerData['description'], JSON_UNESCAPED_UNICODE);

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

            if (!empty($bannerData['name'])) {
                $bannerData['name'] = json_encode($bannerData['name'], JSON_UNESCAPED_UNICODE);
            }
            if (!empty($bannerData['description'])) {
                $bannerData['description'] = json_encode($bannerData['description'], JSON_UNESCAPED_UNICODE);
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

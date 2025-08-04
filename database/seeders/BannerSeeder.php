<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Banner;

class BannerSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $data = [
            [

                "image" => "staticImage/banners/banner1.jpg",
            ],
            [

                "image" => "staticImage/banners/banner2.jpg",
            ],
            [

                "image" => "staticImage/banners/banner3.jpg",
            ],
        ];

        foreach ($data as $row) {
            Banner::updateOrCreate(['image' => $row['image']], $row);
        }
    }
}

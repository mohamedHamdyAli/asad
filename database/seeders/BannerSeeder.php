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
                "name" => json_encode([
                    "en" => "First Banner",
                    "ar" => "البانر الأول"
                ], JSON_UNESCAPED_UNICODE),
                "description" => json_encode([
                    "en" => "First banner description.",
                    "ar" => "وصف البانر الأول"
                ], JSON_UNESCAPED_UNICODE),
                "image" => "staticImage/banners/banner1.jpg",
            ],
            [
                "name" => json_encode([
                    "en" => "Second Banner",
                    "ar" => "البانر الثاني"
                ], JSON_UNESCAPED_UNICODE),
                "description" => json_encode([
                    "en" => "Second banner description.",
                    "ar" => "وصف البانر الثاني"
                ], JSON_UNESCAPED_UNICODE),
                "image" => "staticImage/banners/banner2.jpg",
            ],
            [
                "name" => json_encode([
                    "en" => "Third Banner",
                    "ar" => "البانر الثالث"
                ], JSON_UNESCAPED_UNICODE),
                "description" => json_encode([
                    "en" => "Third banner description.",
                    "ar" => "وصف البانر الثالث"
                ], JSON_UNESCAPED_UNICODE),
                "image" => "staticImage/banners/banner3.jpg",
            ],
        ];

        foreach ($data as $row) {
            Banner::firstOrCreate(['name' => $row['name']], $row);
        }
    }
}

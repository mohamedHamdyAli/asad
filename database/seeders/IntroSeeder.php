<?php

namespace Database\Seeders;

use App\Models\Intro;
use Illuminate\Database\Seeder;

class IntroSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $data = [
            [
                "name" => json_encode([
                    "en" => "first intro",
                    "ar" => "الخدمة الأولى"
                ], JSON_UNESCAPED_UNICODE),
                "description" => json_encode([
                    "en" => "first intro.",
                    "ar" => "الخدمة الأولى"
                ], JSON_UNESCAPED_UNICODE),
                "image" => "staticImage/intro/first.png",
                "order" => 1,
                "type" => 'user',
            ],
            [
                "name" => json_encode([
                    "en" => "second intro",
                    "ar" => "الخدمة الثانيه"
                ], JSON_UNESCAPED_UNICODE),
                "description" => json_encode([
                    "en" => "second intro.",
                    "ar" => "الخدمة الثانيه"
                ], JSON_UNESCAPED_UNICODE),
                "image" => "staticImage/intro/second.png",
                "order" => 2,
                "type" => 'user',

            ],
            [
                "name" => json_encode([
                    "en" => "third intro",
                    "ar" => "الخدمة الثالثة"
                ], JSON_UNESCAPED_UNICODE),
                "description" => json_encode([
                    "en" => "third intro.",
                    "ar" => "الخدمة الثالثة"
                ], JSON_UNESCAPED_UNICODE),
                "image" => "staticImage/intro/third.png",
                "order" => 3,
                "type" => 'user',

            ],
        ];

        foreach ($data as $row) {
            Intro::create($row);
        }
    }
}

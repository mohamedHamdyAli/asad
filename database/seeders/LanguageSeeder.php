<?php

namespace Database\Seeders;

use App\Models\Language;
use App\services\FileService;
use Illuminate\Database\Seeder;

class LanguageSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $Languages = [
            [
                'name' => 'العربية',
                'name_en' => 'Arabic',
                'app_file' => 'ar.json',
                'panel_file' => 'ar_panel.json',
                'car_type_file' => 'ar_car_type.json',
                'car_model_file' => 'ar_car_model.json',
                'car_color_file' => 'ar_car_color.json',
                'is_rtl' => 'true',
                'icon' => 'staticImage/language/arabic.png',
                'country_code' => '+966',
                'code' => 'ar',
            ],
            [
                'name' => 'English',
                'name_en' => 'English',
                'app_file' => 'en.json',
                'panel_file' => 'en_panel.json',
                'car_type_file' => 'en_car_type.json',
                'car_model_file' => 'en_car_model.json',
                'car_color_file' => 'en_car_color.json',
                'is_rtl' => 'false',
                'icon' => 'staticImage/language/english.png',
                'country_code' => '+966',
                'code' => 'en',
            ],
        ];
        foreach ($Languages as $lang) {
            Language::create($lang);
            FileService::generateJsonLanguageFile($lang['code']);
            FileService::generateJsonLanguageFile("{$lang['code']}_app");
            FileService::generateJsonCarTypeFile("{$lang['code']}_car_type");
            FileService::generateJsonCarModelFile("{$lang['code']}_car_model");
            FileService::generateJsonColorFile("{$lang['code']}_car_color");
        }
    }
}

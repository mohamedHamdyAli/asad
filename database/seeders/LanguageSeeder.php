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
                'app_file' => 'ar_app.json',
                'panel_file' => 'ar_panel.json',
                'vendor_file' => 'ar_vendor.json',
                'web_file' => 'ar_web.json',
                'is_rtl' => 'true',
                'icon' => 'staticImage/language/arabic.png',
                'country_code' => '+966',
                'code' => 'ar',
            ],
            [
                'name' => 'English',
                'name_en' => 'English',
                'app_file' => 'en_app.json',
                'panel_file' => 'en_panel.json',
                'vendor_file' => 'en_vendor.json',
                'web_file' => 'en_web.json',
                'is_rtl' => 'false',
                'icon' => 'staticImage/language/english.png',
                'country_code' => '+966',
                'code' => 'en',
            ],
        ];
        foreach ($Languages as $lang) {
            $languageModel = Language::updateOrCreate(
                ['code' => $lang['code']],
                $lang
            );

            if ($languageModel->wasRecentlyCreated) {
                FileService::generateJsonLanguageFile($lang['code']);

                // FileService::generateJsonLanguageFile("{$lang['code']}_app", 'userAppFile');
                // FileService::generateJsonLanguageFile("{$lang['code']}_panel", 'adminDashboardFile');
                // FileService::generateJsonLanguageFile("{$lang['code']}_vendor", 'vendorDashboardFile');
                // FileService::generateJsonLanguageFile("{$lang['code']}_web", 'webFile');
            }
        }
    }
}

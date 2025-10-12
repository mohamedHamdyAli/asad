<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Language extends Model
{
    protected $fillable = [
        'name',
        'name_en',
        'app_file',
        'panel_file',
        'vendor_file',
        'web_file',
        'is_rtl',
        'icon',
        'country_code',
        'code',
        'is_enabled',
    ];

    public function getRtlAttribute($rtl)
    {
        return $rtl != 0;
    }

    public static function getLanguageCode(): array
    {
        return self::where('is_enabled', 1)->pluck('code')->toArray();
    }

    public static function getDefaultLanguage()
    {
        $language = self::select('code')->where('is_enabled', 1)->first();
        if ($language) {
            return $language->code;
        }
        return null;
    }
    public static function getLanguageList()
    {
        $languages = self::where('is_enabled', 1)->get();
        if ($languages) {
            return $languages;
        }
        return null;
    }
}

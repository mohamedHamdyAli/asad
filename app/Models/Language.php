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

    public function getLanguageCode()
    {
        $languages = self::pluck('code')->where('is_enabled', 1)->toArray();
        if ($languages) {
            return $languages;
        }
        return null;
    }
    public function getDefaultLanguage()
    {
        $language = self::select('code')->where('is_enabled', 1)->first();
        if ($language) {
            return $language->code;
        }
        return null;
    }
    public function getLanguageList()
    {
        $languages = self::where('is_enabled', 1)->get();
        if ($languages) {
            return $languages;
        }
        return null;
    }
}

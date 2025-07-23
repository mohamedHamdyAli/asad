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

    public static function getLanguageCode(){
        return self::pluck('code')->where('is_enabled',1)->toArray();
    }
    public static function getDefaultLanguage(){
        return self::select('code')->where('is_enabled',1)->first()->code;
    }
    public static function getLanguageList(){
        return self::where('is_enabled',1)->get();
    }
}

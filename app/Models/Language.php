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
        'is_rtl',
        'icon',
        'country_code',
        'code',
        'is_enabled',
    ];

    public function getRtlAttribute($rtl) {
        return $rtl != 0;
    }

    public function getLanguageCode(){
        return self::pluck('code')->whereIsEnabled(1)->toArray();
    }
    public function getDefaultLanguage(){
        return self::select('code')->whereIsEnabled(1)->first()->code;
    }
    public function getLanguageList(){
        return self::whereIsEnabled(1)->get();
    }
}

<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;


class Intro extends Model
{
    protected $fillable = [
        'name',
        'description',
        'image',
        'order',
        'is_enabled',
    ];
    protected function asJson($value)
    {
        return json_encode($value, JSON_UNESCAPED_UNICODE);
    }
    public function getNameAttribute($value)
    {
        return json_decode($value);
    }
    public function getDescriptionAttribute($value)
    {
        return json_decode($value);
    }

    public static function allActive($isEnable = false)
    {
        if ($isEnable === true) {
            return self::where('is_enabled', 1)->get();
        }
        return self::get();
    }
}

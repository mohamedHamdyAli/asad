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
        'type',
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

    public static function allActive()
    {
        // Fetch all active intros
        return Intro::whereIsEnabled(1)->orderBy('order')->get();
    }
    public static function allUserActive()
    {
        // Fetch all active Users intros
        return Intro::whereIsEnabled(1)->whereType('user')->orderBy('order')->get();
    }

    public static function allVendorActive()
    {
        // Fetch all active Users intros
        return Intro::whereIsEnabled(1)->whereType('vendor')->orderBy('order')->get();
    }
}

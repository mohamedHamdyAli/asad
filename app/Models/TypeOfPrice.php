<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class TypeOfPrice extends Model
{
    use HasFactory;

    protected $fillable = ['title'];

    protected function asJson($value)
    {
        return json_encode($value, JSON_UNESCAPED_UNICODE);
    }
    public function getTitleAttribute($value)
    {
        return json_decode($value);
    }

    public function unitQuote()
    {
        return $this->hasMany(UnitQuote::class, 'type_of_price_id');
    }
    public static function allData()
    {
        return self::all();
    }
}

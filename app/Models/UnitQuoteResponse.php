<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class UnitQuoteResponse extends Model
{
    use HasFactory;

   protected $fillable = ['unit_quote_id','user_id','vendor_id', 'title','price','time_line' ];

    protected function asJson($value)
    {
        return json_encode($value, JSON_UNESCAPED_UNICODE);
    }
    public function getTitleAttribute($value)
    {
        return json_decode($value);
    }
    public function getTimeLineAttribute($value)
    {
        return json_decode($value);
    }

    public function unitQuote()
    {
        return $this->belongsTo(UnitQuote::class, 'unit_quote_id');
    }

    public function vendor()
    {
        return $this->belongsTo(User::class, 'vendor_id');
    }
    public function user()
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    public static function allUserData($userId)
    {
        return self::where('user_id',$userId)->get();
    }
}

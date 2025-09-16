<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class UnitQuoteNote extends Model
{
    protected $fillable = ['unit_quote_id','vendor_id', 'description','status'];

    protected function asJson($value)
    {
        return json_encode($value, JSON_UNESCAPED_UNICODE);
    }
    public function getDescriptionAttribute($value)
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
}

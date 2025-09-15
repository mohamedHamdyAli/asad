<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class UnitQuote extends Model
{
    use HasFactory;
    protected $fillable = ['title', 'other_title', 'user_id', 'type_of_building_id', 'type_of_price_id','pay_image'];

    public function user()
    {
        return $this->belongsTo(User::class, 'user_id');
    }
    public function typeOfBuilding()
    {
        return $this->belongsTo(TypeOfBuilding::class, 'type_of_building_id');
    }
    public function typeOfPrice()
    {
        return $this->belongsTo(TypeOfPrice::class, 'type_of_price_id');
    }

    public function unitQuoteGallery()
    {
        return $this->hasMany(UnitQuoteGallery::class, 'unit_quote_id');
    }
    public function unitQuoteResponse()
    {
        return $this->hasMany(UnitQuoteResponse::class, 'unit_quote_id');
    }
    public function unitQuoteNote()
    {
        return $this->hasMany(UnitQuoteNote::class, 'unit_quote_id');
    }
}

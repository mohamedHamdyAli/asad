<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class UnitQuoteGallery extends Model
{
    use HasFactory;


    protected $fillable = ['unit_quote_id', 'image'];



    public function unitQuote()
    {
        return $this->belongsTo(UnitQuote::class, 'unit_quote_id');
    }
}

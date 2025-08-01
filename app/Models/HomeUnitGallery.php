<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class HomeUnitGallery extends Model
{
    use HasFactory;

    protected $fillable = [
        'unit_id',
        'image',
    ];

    public function unit()
    {
        return $this->belongsTo(Unit::class);
    }
}

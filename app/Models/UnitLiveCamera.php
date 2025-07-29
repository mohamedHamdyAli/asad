<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class UnitLiveCamera extends Model
{
    use HasFactory;

    protected $fillable = [
        'unit_id',
        'ip_address',
        'camera_link',
    ];

    public function unit()
    {
        return $this->belongsTo(Unit::class);
    }
}

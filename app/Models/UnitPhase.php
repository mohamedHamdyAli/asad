<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class UnitPhase extends Model
{
    use HasFactory;

    protected $fillable = ['unit_id', 'status', 'description'];

    public function unit()
    {
        return $this->belongsTo(Unit::class);
    }
}

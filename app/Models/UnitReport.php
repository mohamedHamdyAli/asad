<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class UnitReport extends Model
{
    use HasFactory;


    protected $fillable = [
        'unit_id',
        'title',
        'file',
    ];

    public function unit()
    {
        return $this->belongsTo(Unit::class);
    }
}

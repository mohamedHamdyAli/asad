<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class UnitPhase extends Model
{
    use HasFactory;

    protected $fillable = ['unit_id', 'status', 'description'];

    protected function asJson($value)
    {
        return json_encode($value, JSON_UNESCAPED_UNICODE);
    }
    public function getDescriptionAttribute($value)
    {
        return json_decode($value);
    }
    public function unit()
    {
        return $this->belongsTo(Unit::class);
    }
}

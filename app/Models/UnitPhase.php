<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class UnitPhase extends Model
{
    use HasFactory;

    protected $fillable = ['unit_id', 'status', 'description'];

    public const STATUS_CODE = [
        'site_handover' => 1,
        'skeleton'      => 2,
        'finishing'     => 3,
        'handover'      => 4,
    ];

    public function getStatusCodeAttribute()
    {
        return self::STATUS_CODE[$this->status];
    }


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

    public function notes()
    {
        return $this->hasMany(UnitPhaseNote::class);
    }
}

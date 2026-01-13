<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class UnitPhaseNote extends Model
{
    use HasFactory;

    protected $fillable = ['unit_id', 'unit_phase_id', 'user_id', 'note', 'status'];

    public function unit()
    {
        return $this->belongsTo(Unit::class);
    }

    public function unitPhase()
    {
        return $this->belongsTo(UnitPhase::class);
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}

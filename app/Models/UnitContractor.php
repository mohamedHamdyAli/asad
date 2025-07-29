<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class UnitContractor extends Model
{
    use HasFactory;

    protected $fillable = ['unit_id', 'contractor_id'];

    public function unit()
    {
        return $this->belongsTo(Unit::class);
    }

    public function contractor()
    {
        return $this->belongsTo(Contractor::class);
    }
}

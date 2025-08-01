<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class UnitIssue extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'unit_id',
        'title',
        'description',
    ];
    public function user()
    {
        return $this->belongsTo(User::class);
    }
    public function unit()
    {
        return $this->belongsTo(Unit::class);
    }
}

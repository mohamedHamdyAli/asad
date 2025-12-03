<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ExtensionDate extends Model
{
    protected $fillable = ['unit_id', 'extended_date'];

    public function unit()
    {
        return $this->belongsTo(Unit::class);
    }

}

<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class UnitDocument extends Model
{
    use HasFactory;

    protected $fillable = [
        'unit_id',
        'folder_id',
        'title',
        'file',
    ];

    public function unit()
    {
        return $this->belongsTo(Unit::class);
    }
    public function folder()
    {
        return $this->belongsTo(Folder::class);
    }
}

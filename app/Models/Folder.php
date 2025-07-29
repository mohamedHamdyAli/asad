<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Folder extends Model
{
        use HasFactory;

    protected $fillable = [
        'file_type',
        'name',
        'folder_image',
    ];
}

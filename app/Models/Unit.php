<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Unit extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'location',
        'lat',
        'long',
        'start_date',
        'end_date',
        'cover_image',
        'description',
        'user_id',
        'vendor_id',
    ];
}

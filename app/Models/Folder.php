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

    public function unitDocument()
    {
        return $this->hasMany(UnitDocument::class, 'folder_id');
    }
    public function unitDrawing()
    {
        return $this->hasMany(UnitDrawing::class, 'folder_id');
    }
    public function unitGallery()
    {
        return $this->hasMany(UnitGallery::class, 'folder_id');
    }
    public static function getFolderByType($type) {
        return self::select('id', 'name')->where('file_type',$type)->get();
    }
}

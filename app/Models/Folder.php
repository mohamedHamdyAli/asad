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
        'unit_id',
    ];

    protected function asJson($value)
    {
        return json_encode($value, JSON_UNESCAPED_UNICODE);
    }
    public function getNameAttribute($value)
    {
        return json_decode($value);
    }

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
    // public static function getFolderByType($type) {
    //     return self::select('id', 'name')->where('file_type',$type)->get();
    // }
     public function scopeOfType($query, string $type)
    {
        return $query->where('file_type', $type);
    }

    public function scopeOfUnit($query, $unitId)
    {
        return $query->where('unit_id', $unitId); 
    }

    public static function getFolderByType($type)
    {
        return self::ofType($type)->select('id','name','folder_image')->get();
    }
}

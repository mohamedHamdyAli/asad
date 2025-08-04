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
        'status',
    ];

    protected function asJson($value)
    {
        return json_encode($value, JSON_UNESCAPED_UNICODE);
    }
    public function getNameAttribute($value)
    {
        return json_decode($value);
    }
    public function getDescriptionAttribute($value)
    {
        return json_decode($value);
    }
    public function user()
    {
        return $this->belongsTo(User::class, 'user_id');
    }
    public function vendor()
    {
        return $this->belongsTo(User::class, 'vendor_id');
    }

    public function unitConstulant()
    {
        return $this->hasMany(UnitConstulant::class, 'unit_id');
    }
    public function unitContractor()
    {
        return $this->hasMany(UnitContractor::class, 'unit_id');
    }
    public function unitDocument()
    {
        return $this->hasMany(UnitDocument::class, 'unit_id');
    }
    public function unitDrawing()
    {
        return $this->hasMany(UnitDrawing::class, 'unit_id');
    }
    public function unitGallery()
    {
        return $this->hasMany(UnitGallery::class, 'unit_id');
    }
    public function unitIssue()
    {
        return $this->hasMany(UnitIssue::class, 'unit_id');
    }
    public function unitLiveCamera()
    {
        return $this->hasMany(UnitLiveCamera::class, 'unit_id');
    }
    public function unitPhase()
    {
        return $this->hasMany(UnitPhase::class, 'unit_id');
    }
    public function unitPhaseReport()
    {
        return $this->hasMany(UnitPhaseReport::class, 'unit_id');
    }
    public function unitReport()
    {
        return $this->hasMany(UnitReport::class, 'unit_id');
    }
    public function unitTimeLine()
    {
        return $this->hasMany(UnitTimeLine::class, 'unit_id');
    }
    public function homeUnitGallery()
    {
        return $this->hasMany(HomeUnitGallery::class, 'unit_id');
    }

    public static function allUserUnit($userId, $status = null)
    {
        $query = self::where('user_id', $userId);
        if ($status !== null) {
            $query->where('status', $status);
        }
        return $query->with('homeUnitGallery')->get();
    }
    public static function allVendorUnit($vendorId, $status = null)
    {
        $query = self::where('vendor_id', $vendorId);
        if ($status !== null) {
            $query->where('status', $status);
        }
        return $query->get();
    }
}

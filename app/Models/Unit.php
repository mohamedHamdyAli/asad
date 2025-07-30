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

    public static function allUserUnit($userId, $status = null)
    {
        $query = self::where('user_id', $userId);
        if ($status !== null) {
            $query->where('status', $status);
        }
        return $query->get();
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

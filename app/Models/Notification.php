<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Notification extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'type',
        'objectable_id',
        'objectable_type',
        'title',
        'body',
        'is_seen',
        'seen_time',
    ];


    protected function casts(): array
    {
        return [
            'is_seen' => 'boolean',
            'seen_time' => 'datetime',
        ];
    }

    public static function getUserNotification($id)
    {

        return self::whereUserId($id)->get();
    }
    public static function getvendorNotification()
    {
        return self::whereType('vendor')->whereUserId(vendorAuth()->id)->get();
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function objectable()
    {
        return $this->morphTo();
    }
}

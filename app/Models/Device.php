<?php

namespace App\Models;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Device extends Model
{
     use HasFactory;

    protected $fillable = [
        'user_id',
        'type',
        'device_id',
        'device_type',
        'token',
        'login',
        'notify_status',
        'device_login'
    ];

        public static function storeDevice($request)
    {
        $user = userOrGuestAuth();
        if(!$user){
            return failReturnMsg('Unauthorized user');
        }

        Device::updateOrCreate([
            'device_id'         => $request['device_id'],
            'user_id'           => $user->id,
            'type'              => $user->role,
        ], [
            'device_id'         => $request['device_id'],
            'device_type'       => $request['device_type'],
            'token'             => $user->token()->id,
            'user_id'           => $user->id,
            'login'             => 'active',
        ]);
    }
}

<?php

namespace App\Trait\notifications;

use App\Models\Device;
use App\Models\Notification;

trait NotificationTrait
{

    use FCMTrait;

    public function appNotificationWithObject($data)
    {
        $title = $data['title'];
        $body  = $data['message'];
        $user  = $data['user'];
        $arr = [
            'user_id'     => $user->id,
            'type'   => $user->role,
            'objectable_id'   => $data['objectable']->id,
            'objectable_type' => \get_class($data['objectable']),
            'title'         => $title,
            'body'          => $body,
        ];

        $notification = Notification::create($arr);
        $this->devicesFcm($notification, $data);
    }


    public function appNotification($data)
    {
        $title = $data['title'];
        $body  = $data['message'];
        $user  = $data['user'];

        $arr = [
            'user_id' => $user->id,
            'title'   => $title,
            'body'    => $body,
        ];


        // restrict guest type
        if (!empty($user->role) && $user->role !== 'guest') {
            $arr['type'] = $user->role;
        }

        $notification = Notification::create($arr);
        $this->devicesFcm($notification, $data);
    }

    public function AppDuplicatedNotification($data)
    {
        $title = $data['title'];
        $body  = $data['message'];
        $user  = $data['user'];
        $arr = [
            'user_id'     => $user->id,
            'type'   => $user->role,
            'title'         => $title,
            'body'          => $body,
        ];

        $notification = Notification::updateOrCreate([
            'user_id'     => $user->id,
            'type'   => $user->role,
            'title'         => $title,
            'body'          => $body,
        ], [$arr]);
        $this->devicesFcm($notification, $data);
    }
    public function devicesFcm($notification, $data)
    {
        // Fcm
        $devices = Device::whereUserId($notification->user_id)->get();
        if (count($devices) > 0) {
            $tokens = [];
            $types  = [];
            foreach ($devices as $device) {
                $tokens[] = $device->device_id;
                $types[]  = $device->device_type;
            }

            $this->sendFcm($tokens, $data, $types);
        }
    }
}

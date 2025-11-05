<?php

namespace App\services\Notifications;


use App\Models\Notification;
use Illuminate\Support\Facades\DB;


class NotificationsApiService
{
    public function seenNotification($request)
    {
        $seenNotification = DB::transaction(function () use ($request) {
            $user     = userAuth();

            Notification::where([
                'id' => $request['notification_id'],
                'user_id' => $user->id,
            ])->update([
                'is_seen' => 1,
                'seen_time' => now(),
            ]);
        });

        return $seenNotification;
    }
}

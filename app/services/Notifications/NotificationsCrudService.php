<?php

namespace App\services\Notifications;

use App\Models\User;
use App\Models\Notification;
use App\Trait\notifications\NotificationTrait;
use Illuminate\Support\Facades\DB;

class NotificationsCrudService
{
    use NotificationTrait;

    public function sendNotification(array $data): bool
    {
        return DB::transaction(function () use ($data) {
            $title = $data['title'];
            $body  = $data['body'];

            if (!empty($data['user_id'])) {
                $user = User::find($data['user_id']);
                if (!$user) {
                    return false;
                }

                $this->appNotification([
                    'title'   => $title,
                    'message' => $body,
                    'user'    => $user,
                ]);

                return true;
            }

            $users = User::all();
            foreach ($users as $user) {
                $this->appNotification([
                    'title'   => $title,
                    'message' => $body,
                    'user'    => $user,
                ]);
            }

            return true;
        });
    }


    public function getAllNotifications()
    {
        return Notification::latest()->get();
    }


    public function getUserNotifications(int $user_id)
    {
        return Notification::where('user_id', $user_id)
            ->latest()
            ->get();
    }


    public function deleteNotification(int $id): bool
    {
        $notification = Notification::find($id);
        if (!$notification) return false;

        $notification->delete();
        return true;
    }
}

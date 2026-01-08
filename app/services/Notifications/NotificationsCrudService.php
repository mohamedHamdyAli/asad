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

            if (!empty($data['user_ids']) && is_array($data['user_ids'])) {
                foreach ($data['user_ids'] as $userId) {
                    $user = User::find($userId);
                    if ($user) {
                        $this->appNotification([
                            'title'   => $title,
                            'message' => $body,
                            'user'    => $user,
                        ]);
                    }
                }
                return true;
            }

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
        $notifications = Notification::with('user:id,name,email')
            ->latest()
            ->get()
            ->groupBy(function ($notification) {
                return $notification->title . '|' . $notification->body . '|' . $notification->created_at->format('Y-m-d H:i');
            })
            ->map(function ($group) {
                $first = $group->first();

                $users = $group->map(function ($notif) {
                    return $notif->user;
                })->filter()->values();

                return [
                    'id' => $first->id,
                    'title' => $first->title,
                    'body' => $first->body,
                    'type' => $first->type,
                    'created_at' => $first->created_at,
                    'users' => $users,
                    'users_count' => $users->count(),
                ];
            })
            ->values();

        return $notifications;
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

        Notification::where('title', $notification->title)
            ->where('body', $notification->body)
            ->where('created_at', '>=', $notification->created_at->startOfMinute())
            ->where('created_at', '<', $notification->created_at->copy()->addMinute())
            ->delete();

        return true;
    }
}

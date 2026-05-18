<?php

namespace App\Http\Controllers\Admin;


use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\PushNotificationsRequest;
use App\Models\Notification;
use App\services\Notifications\NotificationsCrudService;
use Illuminate\Http\Request;

class AdminNotificationController extends Controller
{
    protected NotificationsCrudService $notificationsCrudService;

    public function __construct(NotificationsCrudService $notificationsCrudService)
    {
        $this->notificationsCrudService = $notificationsCrudService;
    }

    public function index()
    {
        $notifications = $this->notificationsCrudService->getAllNotifications();

        return response()->json([
            'status' => true,
            'data'   => $notifications
        ]);
    }

    public function store(PushNotificationsRequest $request)
    {

        $request->validated();
        $success = $this->notificationsCrudService->sendNotification($request->all());
        if ($success) {
            return response()->json([
                'status'  => true,
                'message' => 'Notification sent successfully'
            ]);
        }

        return response()->json([
            'status'  => false,
            'message' => 'Failed to send notification'
        ], 400);
    }

    public function show($id)
    {
        $notifications = $this->notificationsCrudService->getUserNotifications($id);

        if ($notifications->isEmpty()) {
            return response()->json([
                'status'  => false,
                'message' => 'No notifications found for this user'
            ], 404);
        }

        return response()->json([
            'status' => true,
            'data'   => $notifications
        ]);
    }

    public function destroy($id)
    {
        $deleted = $this->notificationsCrudService->deleteNotification($id);

        if (!$deleted) {
            return response()->json([
                'status'  => false,
                'message' => 'Notification not found'
            ], 404);
        }

        return response()->json([
            'status'  => true,
            'message' => 'Notification deleted successfully'
        ]);
    }

    public function seen(Request $request)
    {
        $id = $request->input('notification_id');
        if (!$id) {
            return response()->json([
                'status'  => false,
                'message' => 'notification_id is required',
            ], 422);
        }

        $userId = $request->user()?->id;
        $query = Notification::where('id', $id);
        if ($userId) {
            $query->where('user_id', $userId);
        }

        $query->update([
            'is_seen'   => 1,
            'seen_time' => now(),
        ]);

        return response()->json([
            'status'  => true,
            'message' => 'Notification marked as seen',
        ]);
    }

    public function seenAll(Request $request)
    {
        $userId = $request->user()?->id;
        if (!$userId) {
            return response()->json([
                'status'  => false,
                'message' => 'Unauthenticated',
            ], 401);
        }

        Notification::where('user_id', $userId)
            ->where('is_seen', 0)
            ->update([
                'is_seen'   => 1,
                'seen_time' => now(),
            ]);

        return response()->json([
            'status'  => true,
            'message' => 'All notifications marked as seen',
        ]);
    }
}

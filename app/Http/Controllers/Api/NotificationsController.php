<?php

namespace App\Http\Controllers\Api;

use App\Models\Device;
use App\Models\Notification;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Http\Requests\Api\DeviceRequest;
use App\Http\Requests\Api\NotificationId;
use App\Http\Resources\NotificationResource;
use App\services\Notifications\NotificationsApiService;

class NotificationsController extends Controller
{
    public function sendDeviceInfo(DeviceRequest $request)
    {
        Device::storeDevice($request->validated());
        return returnSuccessMsg('Device info stored successfully');
    }

    public function deleteNotification(Request $request)
    {
        $notification = Notification::find($request->input('notification_id'));
        if ($notification) {
            $notification->delete();
            return returnSuccessMsg('Notification deleted successfully');
        }

        return failReturnMsg('Notification not found');
    }

    public function seenNotification(NotificationId $request)
    {
        (new NotificationsApiService())->seenNotification($request->validated());
        return returnSuccessMsg( 'notification seen successfully');
    }

    public function getUserNotification()
    {
        $data = NotificationResource::collection(Notification::getUserNotification());
        return successReturnData($data, ('User notifications retrieved successfully'));
    }
}

<?php

namespace App\Trait\notifications;

use App\Models\Device;
use App\Models\Notification;
use App\Models\Unit;
use App\Models\User;
use Illuminate\Support\Facades\Log;

trait NotifiesUnitOwnerTrait
{
    use NotificationTrait;

    /**
     * Persist a notification for the unit's owner and send FCM push.
     * Use {unit} placeholder in $bodyTemplate to inject the unit's display name.
     */
    protected function notifyUnitOwner(
        Unit $unit,
        string $title,
        string $bodyTemplate,
        ?User $actor = null
    ): void {
        $userId = $unit->user_id;
        if (!$userId) {
            return;
        }

        if ($actor && (int) $actor->id === (int) $userId) {
            return;
        }

        $user = User::find($userId);
        if (!$user) {
            return;
        }

        $unitName = $this->extractUnitName($unit);
        $message = str_replace('{unit}', $unitName, $bodyTemplate);

        try {
            Notification::create([
                'user_id'         => $user->id,
                'type'            => 'user',
                'objectable_id'   => $unit->id,
                'objectable_type' => Unit::class,
                'title'           => $title,
                'body'            => $message,
            ]);

            $devices = Device::whereUserId($user->id)->get();
            if ($devices->isNotEmpty()) {
                $tokens = $devices->pluck('device_id')->all();
                $types  = $devices->pluck('device_type')->all();
                $this->sendFcm($tokens, [
                    'title' => $title,
                    'body'  => $message,
                ], $types);
            }
        } catch (\Throwable $e) {
            Log::error('Unit owner notification failed: ' . $e->getMessage());
        }
    }

    protected function extractUnitName(Unit $unit): string
    {
        $name = $unit->name;
        if (is_string($name)) {
            return $name;
        }
        if (is_array($name)) {
            return (string) ($name['en'] ?? $name['ar'] ?? reset($name) ?: ('#' . $unit->id));
        }
        if (is_object($name)) {
            return (string) ($name->en ?? $name->ar ?? ('#' . $unit->id));
        }
        return '#' . $unit->id;
    }
}

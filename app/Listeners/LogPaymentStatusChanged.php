<?php

namespace App\Listeners;

use App\Events\PaymentStatusChanged;
use App\Models\UnitPaymentLog;
use Illuminate\Support\Facades\Auth;
use Illuminate\Contracts\Queue\ShouldQueue;

class LogPaymentStatusChanged implements ShouldQueue
{
    public function handle(PaymentStatusChanged $event): void
    {
        // skip if no real change
        if ($event->oldStatus === $event->newStatus) {
            return;
        }

        $installment = $event->installment;

        // Prevent duplicate consecutive logs for same new status
        $lastLog = UnitPaymentLog::where('model_type', get_class($installment))
            ->where('model_id', $installment->id)
            ->orderByDesc('created_at')
            ->first();

        if ($lastLog) {
            $lastNewStatus = $lastLog->new_data['status'] ?? null;
            if ($lastNewStatus === $event->newStatus) {
                // already logged this transition recently
                return;
            }
        }

        UnitPaymentLog::create([
            'model_type'  => get_class($installment),
            'model_id'    => $installment->id,
            'user_id'     => Auth::id(),
            'action'      => 'status_changed',
            'description' => "Installment #{$installment->id} status changed from {$event->oldStatus} to {$event->newStatus}",
            'old_data'    => ['status' => $event->oldStatus],
            'new_data'    => ['status' => $event->newStatus],
        ]);
    }
}

<?php
/**
 * Listener: LogPaymentStatusChanged
 *
 * Logs any installment status change into unit_payment_logs table.
 */

namespace App\Listeners;

use App\Events\PaymentStatusChanged;
use App\Models\UnitPaymentLog;
use Illuminate\Support\Facades\Auth;
use Illuminate\Contracts\Queue\ShouldQueue;

class LogPaymentStatusChanged implements ShouldQueue
{
    public function handle(PaymentStatusChanged $event): void
    {
        $installment = $event->installment;

        UnitPaymentLog::create([
            'unit_payment_id' => $installment->unit_payment_id,
            'unit_payment_installment_id' => $installment->id,
            'user_id' => Auth::id(),
            'action' => 'status_changed',
            'description' => "Installment status changed from {$event->oldStatus} to {$event->newStatus}",
            'changes' => [
                'old' => $event->oldStatus,
                'new' => $event->newStatus,
            ],
        ]);
    }
}

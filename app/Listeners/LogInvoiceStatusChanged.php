<?php

namespace App\Listeners;

use App\Events\InvoiceStatusChanged;
use App\Models\UnitPaymentLog;
use Illuminate\Support\Facades\Auth;

class LogInvoiceStatusChanged
{
    public function handle(InvoiceStatusChanged $event): void
    {
        // skip if no real change
        if ($event->oldStatus === $event->newStatus) {
            return;
        }

        $invoice = $event->invoice;

        // Prevent duplicate consecutive logs for same new status
        $lastLog = UnitPaymentLog::where('model_type', get_class($invoice))
            ->where('model_id', $invoice->id)
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
            'model_type'  => get_class($invoice),
            'model_id'    => $invoice->id,
            'user_id'     => Auth::id(),
            'action'      => 'status_changed',
            'description' => "Invoice #{$invoice->id} status changed from {$event->oldStatus} to {$event->newStatus}",
            'old_data'    => ['status' => $event->oldStatus],
            'new_data'    => ['status' => $event->newStatus],
        ]);
    }
}

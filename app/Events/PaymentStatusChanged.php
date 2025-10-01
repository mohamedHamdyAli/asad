<?php
/**
 * Event: PaymentStatusChanged
 *
 * Fired when an installment's status changes (e.g., pending -> paid).
 * Payload: the installment model and old/new status values.
 *
 * This event is intended for audit/logging/notifications.
 */

namespace App\Events;

use App\Models\UnitPaymentInstallment;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class PaymentStatusChanged
{
    use Dispatchable, SerializesModels;

    public function __construct(
        public UnitPaymentInstallment $installment,
        public string $oldStatus,
        public string $newStatus
    ) {}
}

<?php
/**
 * Event: InvoiceStatusChanged
 *
 * Fired when an invoice status changes (e.g., pending -> confirmed or rejected).
 * Payload: the invoice and the old/new statuses.
 *
 * Listeners should react by recalculating related installment/payment statuses.
 */

namespace App\Events;

use App\Models\UnitPaymentInstallmentInvoice;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class InvoiceStatusChanged
{
    use Dispatchable, SerializesModels;

    public function __construct(
        public UnitPaymentInstallmentInvoice $invoice,
        public string $oldStatus,
        public string $newStatus
    ) {}
}

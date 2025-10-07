<?php
/**
 * Event: InvoiceUploaded
 *
 * Fired when a user uploads an invoice file from the mobile/web app.
 * Payload: the UnitPaymentInstallmentInvoice model instance (just created).
 *
 * This event is intended to trigger automatic handling (e.g. auto-confirm,
 * notify admins, or other business logic).
 */

namespace App\Events;

use App\Models\UnitPaymentInstallmentInvoice;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class InvoiceUploaded
{
    use Dispatchable, SerializesModels;

    public function __construct(
        public UnitPaymentInstallmentInvoice $invoice
    ) {}
}

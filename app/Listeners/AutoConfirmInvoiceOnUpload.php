<?php
/**
 * Listener: AutoConfirmInvoiceOnUpload
 *
 * Handles InvoiceUploaded event. Current behavior:
 *  - Auto-confirm uploaded invoice (set status = 'confirmed', set payment_date = now())
 *    (Change this behavior if you want admin approval instead.)
 *  - Dispatch InvoiceStatusChanged to trigger recalculation of installment/payment.
 *
 * NOTE: If you prefer to require manual approval, remove auto-confirm logic here
 * and let admin use the confirm endpoint which dispatches InvoiceStatusChanged.
 */

namespace App\Listeners;

use App\Events\InvoiceUploaded;
use App\Events\InvoiceStatusChanged;
use Illuminate\Contracts\Queue\ShouldQueue;

class AutoConfirmInvoiceOnUpload implements ShouldQueue
{
    /**
     * Handle the event.
     */
    public function handle(InvoiceUploaded $event): void
    {
        $invoice = $event->invoice;

        // If already confirmed/rejected, nothing to do.
        if ($invoice->status === 'confirmed' || $invoice->status === 'rejected') {
            return;
        }

        // AUTO-CONFIRM logic:
        // - mark invoice as confirmed
        // - set payment_date if not set
        // - save and fire InvoiceStatusChanged
        $oldStatus = $invoice->status;
        $invoice->status = 'confirmed';
        $invoice->payment_date = $invoice->payment_date ?? now();
        $invoice->save();

        event(new InvoiceStatusChanged($invoice, $oldStatus, 'confirmed'));
    }
}

<?php

namespace App\Providers;

use Illuminate\Foundation\Support\Providers\EventServiceProvider as ServiceProvider;

class EventServiceProvider extends ServiceProvider
{
    /**
     * The event to listener mappings for the application.
     *
     * @var array<class-string, array<int, class-string>>
     */
    protected $listen = [
        // When an invoice file is uploaded
        \App\Events\InvoiceUploaded::class => [
            \App\Listeners\AutoConfirmInvoiceOnUpload::class,
        ],

        // When invoice status changes (confirm / reject)
        \App\Events\InvoiceStatusChanged::class => [
            \App\Listeners\UpdateInstallmentOnInvoiceStatusChanged::class,
            \App\Listeners\LogInvoiceStatusChanged::class,
        ],

        // When installment/payment status changes (for logging/notifications)
        \App\Events\PaymentStatusChanged::class => [
            \App\Listeners\LogPaymentStatusChanged::class,
        ],
    ];

    public function boot(): void
    {
        parent::boot();
    }
}

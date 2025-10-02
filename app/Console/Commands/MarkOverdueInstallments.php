<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Carbon\Carbon;
use App\Models\UnitPaymentInstallment;
use App\Models\UnitPayment;
use App\Events\PaymentStatusChanged;

class MarkOverdueInstallments extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'installments:mark-overdue';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Mark installments as overdue if due_date passed and not fully paid';

    public function handle(): int
    {
        $now = Carbon::now();

        // 1) Select candidate installments: statuses that can become overdue
        $candidates = UnitPaymentInstallment::query()
            ->whereIn('status', ['unpaid', 'pending', 'partial'])
            ->whereNotNull('due_date')
            ->where('due_date', '<', $now)
            ->get();

        $this->info("Found {$candidates->count()} candidate(s) to check for overdue.");

        $processedUnitPayments = [];

        foreach ($candidates as $candidate) {
            DB::transaction(function () use ($candidate, &$processedUnitPayments) {
                // re-fetch with lock to avoid race condition
                $installment = UnitPaymentInstallment::where('id', $candidate->id)
                    ->lockForUpdate()
                    ->first();

                if (!$installment) {
                    return;
                }

                // check status again (could have changed)
                if (!in_array($installment->status, ['unpaid', 'pending', 'partial'])) {
                    // nothing to do
                    return;
                }

                $oldStatus = $installment->status;

                // verify fully unpaid/partially paid? We use invoice sums to decide if still unpaid/partial
                // but business rule here: if due_date passed and not fully paid -> set overdue
                $installment->status = 'overdue';
                $installment->save();

                // Fire PaymentStatusChanged so listeners (logging / notifications) run
                event(new PaymentStatusChanged($installment, $oldStatus, 'overdue'));

                // Collect related unit_payment id to recompute summary later
                $processedUnitPayments[$installment->unit_payment_id] = true;
            });
        }

        // Recompute parent unit_payments summaries for all affected unit payments
        foreach (array_keys($processedUnitPayments) as $unitPaymentId) {
            DB::transaction(function () use ($unitPaymentId) {
                $unitPayment = UnitPayment::where('id', $unitPaymentId)->lockForUpdate()->first();
                if (!$unitPayment)
                    return;

                $totalInstallments = $unitPayment->installments()->count();
                $paidInstallments = $unitPayment->installments()->where('status', 'paid')->count();
                $overdueInstallments = $unitPayment->installments()->where('status', 'overdue')->count();

                $remaining = max(0, $totalInstallments - $paidInstallments);

                $unitPayment->remaining_installments = $remaining;

                if ($remaining === 0) {
                    $unitPayment->overall_status = 'completed';
                } elseif ($overdueInstallments > 0) {
                    $unitPayment->overall_status = 'overdue';
                } elseif ($paidInstallments > 0) {
                    $unitPayment->overall_status = 'in_progress';
                } else {
                    $unitPayment->overall_status = 'pending';
                }

                $unitPayment->save();
            });
        }

        $this->info('Overdue check completed.');

        return 0;
    }
}

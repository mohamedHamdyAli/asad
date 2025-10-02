<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Unit;
use App\Models\UnitPayment;
use Illuminate\Http\Request;

class AdminUnitPaymentController extends Controller
{
    /**
     * Create new payment plan for a unit
     */
    public function store(Request $request, Unit $unit)
    {
        $data = $request->validate([
            'total_price' => 'required|numeric|min:0',
            'installments_count' => 'required|integer|min:1',
            'payment_type' => 'required|in:cash,installments',
        ]);

        $payment = $unit->payments()->create([
            'total_price' => $data['total_price'],
            'installments_count' => $data['installments_count'],
            'remaining_installments' => $data['installments_count'],
            'payment_type' => $data['payment_type'],
            'overall_status' => 'pending',
        ]);

        return response()->json(['message' => 'Payment plan created', 'data' => $payment]);
    }

    /**
     * Update payment plan
     */
    public function update(Request $request, UnitPayment $unitPayment)
    {
        $data = $request->validate([
            'total_price' => 'nullable|numeric|min:0',
            'installments_count' => 'nullable|integer|min:1',
            'payment_type' => 'nullable|in:cash,installments',
            'overall_status' => 'nullable|in:pending,in_progress,completed,overdue',
        ]);

        $unitPayment->update($data);

        return response()->json(['message' => 'Payment plan updated', 'data' => $unitPayment]);
    }

    /**
     * Delete payment plan
     */
    public function destroy(UnitPayment $unitPayment)
    {
        $unitPayment->delete();

        return response()->json(['message' => 'Payment plan deleted']);
    }

    /**
     * Show payment plan details with summary
     */
    public function show(Unit $unit)
    {
        $payments = $unit->payments()->with('installments.invoices')->get();

        return response()->json(['unit' => $unit, 'payments' => $payments]);
    }
}

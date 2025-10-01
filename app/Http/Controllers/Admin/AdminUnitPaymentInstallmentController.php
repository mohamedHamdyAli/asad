<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\UnitPayment;
use App\Models\UnitPaymentInstallment;
use Illuminate\Http\Request;

class AdminUnitPaymentInstallmentController extends Controller
{
    /**
     * List installments of a payment
     */
    public function index(UnitPayment $unitPayment)
    {
        return response()->json($unitPayment->installments()->with('invoices')->get());
    }

    /**
     * Create new installment
     */
    public function store(Request $request, UnitPayment $unitPayment)
    {
        $data = $request->validate([
            'title' => 'required|string|max:255',
            'description' => 'nullable|string',
            'percentage' => 'nullable|numeric|min:0|max:100',
            'amount' => 'required|numeric|min:0',
            'milestone_date' => 'nullable|date',
            'submission_date' => 'nullable|date',
            'consultant_approval_date' => 'nullable|date',
            'due_date' => 'nullable|date',
        ]);

        $installment = $unitPayment->installments()->create($data);

        return response()->json(['message' => 'Installment created', 'data' => $installment]);
    }

    /**
     * Update installment
     */
    public function update(Request $request, UnitPaymentInstallment $installment)
    {
        $data = $request->validate([
            'title' => 'nullable|string|max:255',
            'description' => 'nullable|string',
            'percentage' => 'nullable|numeric|min:0|max:100',
            'amount' => 'nullable|numeric|min:0',
            'status' => 'nullable|in:pending,paid,overdue',
            'milestone_date' => 'nullable|date',
            'submission_date' => 'nullable|date',
            'consultant_approval_date' => 'nullable|date',
            'due_date' => 'nullable|date',
            'payment_date' => 'nullable|date',
        ]);

        $installment->update($data);

        return response()->json(['message' => 'Installment updated', 'data' => $installment]);
    }

    /**
     * Delete installment
     */
    public function destroy(UnitPaymentInstallment $installment)
    {
        $installment->delete();

        return response()->json(['message' => 'Installment deleted']);
    }

    /**
     * Get invoices of installment
     */
    public function getInvoices(UnitPaymentInstallment $installment)
    {
        return response()->json($installment->invoices);
    }

    /**
     * Confirm invoice
     */
    public function confirmInvoice($invoiceId)
    {
        return response()->json(['message' => 'Invoice confirmed', 'id' => $invoiceId]);
    }

    /**
     * Reject invoice
     */
    public function rejectInvoice($invoiceId)
    {
        return response()->json(['message' => 'Invoice rejected', 'id' => $invoiceId]);
    }
}

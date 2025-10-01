<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class UnitPaymentInstallment extends Model
{
    use HasFactory;

    protected $fillable = [
        'unit_payment_id',
        'title',
        'description',
        'percentage',
        'amount',
        'invoice',
        'milestone_date',
        'submission_date',
        'consultant_approval_date',
        'payment_date',
        'due_date',
        'status',
    ];

    // Relationship: belongs to payment plan
    public function paymentPlan()
    {
        return $this->belongsTo(UnitPayment::class, 'unit_payment_id');
    }

    // Relationship: one installment can have many invoices (partial payments)
    public function invoices()
    {
        return $this->hasMany(UnitPaymentInstallmentInvoice::class);
    }
}

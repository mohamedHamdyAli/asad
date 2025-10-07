<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class UnitPaymentInstallmentInvoice extends Model
{
    use HasFactory;

    protected $fillable = [
        'unit_payment_installment_id',
        'paid_amount',
        'invoice_file',
        'payment_date',
        'status',
    ];

    // Relationship: belongs to one installment
    public function installment()
    {
        return $this->belongsTo(UnitPaymentInstallment::class, 'unit_payment_installment_id');
    }

        protected function asJson($value)
    {
        return json_encode($value, JSON_UNESCAPED_UNICODE);
    }
    public function getTitleAttribute($value)
    {
        return json_decode($value);
    }
    public function getDescriptionAttribute($value)
    {
        return json_decode($value);
    }
}

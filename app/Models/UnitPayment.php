<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class UnitPayment extends Model
{
    use HasFactory;

    protected $fillable = [
        'unit_id',
        'total_price',
        'installments_count',
        'remaining_installments',
        'payment_type',
        'overall_status',
    ];

    // Relationship: One payment plan has many installments
    public function installments()
    {
        return $this->hasMany(UnitPaymentInstallment::class);
    }

    public function unit()
    {
        return $this->belongsTo(Unit::class);
    }
}

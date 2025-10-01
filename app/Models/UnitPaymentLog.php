<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class UnitPaymentLog extends Model
{
    use HasFactory;

    protected $fillable = [
        'model_type',
        'model_id',
        'action',
        'old_data',
        'new_data',
        'user_id',
    ];

    protected $casts = [
        'old_data' => 'array',
        'new_data' => 'array',
    ];

    // Polymorphic relation: the logged model
    public function loggable()
    {
        return $this->morphTo(__FUNCTION__, 'model_type', 'model_id');
    }

    // Relation to user who made the change
    public function user()
    {
        return $this->belongsTo(User::class);
    }
}

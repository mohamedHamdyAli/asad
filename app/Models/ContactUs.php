<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ContactUs extends Model
{
    protected $fillable = [
        'country',
        'telephone',
        'account_name',
        'bank_name',
        'iban',
        'currency',
        'swift_code',
    ];

    
}

<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Contractor extends Model
{
    use HasFactory;

    protected $fillable = [
        'title',
        'description',
        'image',
        'email',
        'company_address',
        'company_phone',
        'representative_name',
        'representative_phone',
    ];
    

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
    public function getCompanyAddressAttribute($value)
    {
        return json_decode($value);
    }
    public function getRepresentativeNameAttribute($value)
    {
        return json_decode($value);
    }
}

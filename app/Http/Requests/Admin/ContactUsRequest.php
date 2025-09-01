<?php

namespace App\Http\Requests\Admin;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Contracts\Validation\Validator;
use Illuminate\Support\Facades\Route;

class ContactUsRequest extends FormRequest
{

    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        $rules = (Route::is('contact-infos.store')) ? [
            'country'       => 'required|in:Egypt,Kuwait',
            'telephone'     => 'required|string',
            'account_name'  => 'required|string',
            'bank_name'     => 'required|string',
            'iban'          => 'required|string|unique:contact_us,iban',
            'currency'      => 'required|string',
            'swift_code'    => 'required|string',
        ] : [
            'country'       => 'nullable|in:Egypt,Kuwait',
            'telephone'     => 'nullable|string',
            'account_name'  => 'nullable|string',
            'bank_name'     => 'nullable|string',
            'iban'          => 'nullable|string|unique:contact_us,iban',
            'currency'      => 'nullable|string',
            'swift_code'    => 'nullable|string',
        ];

        return $rules;
    }

    protected function failedValidation(Validator $validator)
    {
        failedValidation($validator);
    }
}

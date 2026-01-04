<?php

namespace App\Http\Requests\Admin;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Contracts\Validation\Validator;
use Illuminate\Support\Facades\Route;
use Illuminate\Validation\Rule;


class ContactUsRequest extends FormRequest
{

    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        $id = $this->route('id');

        if (Route::is('contact-infos.store')) {
            return [
                'country'       => 'required|in:Egypt,Kuwait',
                'telephone'     => 'required|string',
                'account_name'  => 'required|string',
                'bank_name'     => 'required|string',
                'iban'          => 'required|string|unique:contact_us,iban',
                'currency'      => 'required|string',
                'swift_code'    => 'required|string',
                'lat'           => 'required|numeric|between:-90,90',
                'long'          => 'required|numeric|between:-180,180',
                'location'      => 'required|string',
            ];
        }

        return [
            'country'       => 'nullable|in:Egypt,Kuwait',
            'telephone'     => 'nullable|string',
            'account_name'  => 'nullable|string',
            'bank_name'     => 'nullable|string',
            'iban'          => [
                'nullable',
                'string',
                Rule::unique('contact_us', 'iban')->ignore($id),
            ],
            'currency'      => 'nullable|string',
            'swift_code'    => 'nullable|string',
            'lat'           => 'nullable|numeric|between:-90,90',
            'long'          => 'nullable|numeric|between:-180,180',
            'location'      => 'nullable|string',
        ];
    }


    protected function failedValidation(Validator $validator)
    {
        failedValidation($validator);
    }
}

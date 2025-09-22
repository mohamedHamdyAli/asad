<?php

namespace App\Http\Requests\Admin;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Contracts\Validation\Validator;
use Illuminate\Support\Facades\Route;

class UnitQuoteResponseRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        if (Route::is('unit-quote-responses.store')) {
            return [
                'data' => 'required|array',
                'data.unit_quote_id' => 'required|exists:unit_quotes,id',
                'data.vendor_id'     => 'required|exists:users,id',
                'data.user_id'       => 'required|exists:users,id',

                'data.title'         => 'required|array',
                'data.title.ar'      => 'required|string',
                'data.title.en'      => 'required|string',

                'data.price'         => 'nullable|numeric|min:0',

                'data.time_line'     => 'nullable|array',
                'data.time_line.ar'  => 'nullable|string',
                'data.time_line.en'  => 'nullable|string',
            ];
        }

        return [
            'data' => 'required|array',
            'data.unit_quote_id' => 'nullable|exists:unit_quotes,id',
            'data.vendor_id'     => 'nullable|exists:users,id',
            'data.user_id'       => 'nullable|exists:users,id',

            'data.title'         => 'nullable|array',
            'data.title.ar'      => 'nullable|string',
            'data.title.en'      => 'nullable|string',

            'data.price'         => 'nullable|numeric|min:0',

            'data.time_line'     => 'nullable|array',
            'data.time_line.ar'  => 'nullable|string',
            'data.time_line.en'  => 'nullable|string',
        ];
    }

    protected function failedValidation(Validator $validator)
    {
        failedValidation($validator);
    }
}

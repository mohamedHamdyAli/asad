<?php

namespace App\Http\Requests\Admin;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Contracts\Validation\Validator;
use Illuminate\Support\Facades\Route;

class TypeOfPriceRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        if (Route::is('type-of-prices.store')) {
            return [
                'data' => 'required|array',
                'data.*.title' => 'required|array',
                'data.*.title.*' => 'required|string|max:255',
            ];
        }

        return [
            'data' => 'required|array',
            'data.title' => 'nullable|array',
            'data.title.*' => 'nullable|string|max:255',
        ];
    }

    protected function failedValidation(Validator $validator)
    {
        failedValidation($validator);
    }
}

<?php

namespace App\Http\Requests\Admin;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Contracts\Validation\Validator;
use Illuminate\Support\Facades\Route;

class ConsultantRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        if (Route::is('consultants.store')) {
            return [
                'data' => 'required|array',
                'data.*.title' => 'required|array',
                'data.*.title.*' => 'required|string|max:255',
                'data.*.description' => 'required|array',
                'data.*.description.*' => 'required|string|max:255',
                'data.*.image' => 'required|image|mimes:jpeg,png,jpg|max:2048',
                'data.*.email' => 'required|email|max:255|unique:consultants,email',
                'data.*.company_phone' => 'nullable|string',
                'data.*.representative_phone' => 'nullable|string',
                'data.*.company_address' => 'nullable|array',
                'data.*.company_address.*' => 'nullable|string|max:255',
                'data.*.representative_name' => 'nullable|array',
                'data.*.representative_name.*' => 'nullable|string|max:255',

            ];
        }

        return [
            'data' => 'required|array',
            'data.title' => 'nullable|array',
            'data.title.*' => 'nullable|string|max:255',
            'data.description' => 'nullable|array',
            'data.description.*' => 'nullable|string|max:255',
            'data.image' => 'nullable|image|mimes:jpeg,png,jpg|max:2048',
            'data.email' => 'nullable|email|max:255|unique:consultants,email,' . $this->route('id'),
            'data.company_phone' => 'nullable|string',
            'data.representative_phone' => 'nullable|string',
            'data.company_address' => 'nullable|array',
            'data.company_address.*' => 'nullable|string|max:255',
            'data.representative_name' => 'nullable|array',
            'data.representative_name.*' => 'nullable|string|max:255',
        ];
    }

    protected function failedValidation(Validator $validator)
    {
        failedValidation($validator);
    }
}

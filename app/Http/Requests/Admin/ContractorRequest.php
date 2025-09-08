<?php

namespace App\Http\Requests\Admin;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Contracts\Validation\Validator;
use Illuminate\Support\Facades\Route;

class ContractorRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        if (Route::is('contractors.store')) {
            return [
                'data' => 'required|array',
                'data.*.title' => 'required|array',
                'data.*.title.*' => 'required|string|max:255',
                'data.*.description' => 'required|array',
                'data.*.description.*' => 'required|string|max:255',
                'data.*.image' => 'required|image|mimes:jpeg,png,jpg|max:2048',
                'data.*.email' => 'required|email|max:255|unique:contractors,email',

            ];
        }
        return [
            'data' => 'required|array',
            'data.title' => 'nullable|array',
            'data.title.*' => 'nullable|string|max:255',
            'data.description' => 'nullable|array',
            'data.description.*' => 'nullable|string|max:255',
            'data.image' => 'nullable|image|mimes:jpeg,png,jpg|max:2048',
            'data.email' => 'nullable|email|max:255|unique:contractors,email,' . $this->route('id'),
        ];
    }

    protected function failedValidation(Validator $validator)
    {
        failedValidation($validator);
    }
}

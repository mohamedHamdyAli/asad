<?php

namespace App\Http\Requests\Admin;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Contracts\Validation\Validator;
use Illuminate\Support\Facades\Route;

class BannerRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     */
    public function rules(): array
    {
        $rules = (Route::is('banners.store')) ? [
            'is_enabled' => 'required|boolean',
            'image' => 'required|mimes:jpeg,png,jpg,svg|max:6144',
        ] : [
            'image' => 'sometimes|mimes:jpeg,png,jpg,svg|max:6144',
            'is_enabled' => 'sometimes|boolean',
        ];

        return $rules;
    }

    protected function failedValidation(Validator $validator)
    {
        failedValidation($validator);
    }
}

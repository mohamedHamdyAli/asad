<?php

namespace App\Http\Requests\Admin;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Contracts\Validation\Validator;

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
        $rules = [

            'image' => 'required|mimes:jpeg,png,jpg,svg|max:6144',
            'is_enabled'    => 'required|boolean',
        ];

        if ($this->isMethod('PUT')) {

            $rules['image'] = 'nullable|mimes:jpeg,png,jpg,svg|max:6144';
            $rules['is_enabled']    = 'nullable|boolean';
        }

        return $rules;
    }

    /**
     * Handle failed validation.
     */
    protected function failedValidation(Validator $validator)
    {
        failedValidation($validator);
    }
}

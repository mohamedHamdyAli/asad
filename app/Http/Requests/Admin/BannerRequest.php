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
        if ($this->route()->getName() === 'banners.store') {
            $rules = [
                'is_enabled'    => 'required|boolean',
                'image'         => 'required|mimes:jpeg,png,jpg,svg|max:6144',
            ];
        } else {
            $rules = [
                'image'         => 'sometimes|mimes:jpeg,png,jpg,svg|max:6144',
                'is_enabled'    => 'sometimes|boolean',
            ];
        }

        return $rules;
    }

    protected function failedValidation(Validator $validator)
    {
        failedValidation($validator);
    }
}

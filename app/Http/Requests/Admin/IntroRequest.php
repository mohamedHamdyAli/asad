<?php

namespace App\Http\Requests\Admin;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Contracts\Validation\Validator;

class IntroRequest extends FormRequest
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
            'name'          => 'required|array',
            'name.*'        => 'required|string',
            'description'   => 'required|array',
            'description.*' => 'required|string',
            'image'         => 'required|mimes:jpeg,png,jpg,svg|max:6144',
            'order'         => 'required|integer',
        ];

        if ($this->isMethod('PUT')) {
            $rules['name']          = 'nullable|array';
            $rules['name.*']        = 'nullable|string';
            $rules['description']   = 'nullable|array';
            $rules['description.*'] = 'nullable|string';
            $rules['image']         = 'nullable|mimes:jpeg,png,jpg,svg|max:6144';
            $rules['order']         = 'nullable|integer';
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

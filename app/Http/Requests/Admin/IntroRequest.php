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
    if ($this->route()->getName() === 'intro.store') {

            $rules = [
                'name'          => 'required|array',
                'name.*'        => 'required|string',
                'description'   => 'required|array',
                'description.*' => 'required|string',
                'image'         => 'required|mimes:jpeg,png,jpg,svg|max:6144',
                'order'         => 'required|integer',
            ];
        } else {
            $rules = [

                'name'          => 'nullable|array',
                'name.*'        => 'nullable|string',
                'description'   => 'nullable|array',
                'description.*' => 'nullable|string',
                'image'         => 'nullable|mimes:jpeg,png,jpg,svg|max:6144',
                'order'         => 'nullable|integer',
            ];
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

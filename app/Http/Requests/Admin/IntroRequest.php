<?php

namespace App\Http\Requests\Admin;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Contracts\Validation\Validator;
use Illuminate\Support\Facades\Route;

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
        $rules = (Route::is('intro.store')) ? [
            'name' => 'required|array',
            'name.*' => 'required|string',
            'description' => 'required|array',
            'description.*' => 'required|string',
            'image' => 'required|mimes:jpeg,png,jpg,svg|max:6144',
            'order' => 'required|integer',
        ] : [

            'name' => 'nullable|array',
            'name.*' => 'nullable|string',
            'description' => 'nullable|array',
            'description.*' => 'nullable|string',
            'image' => 'nullable|mimes:jpeg,png,jpg,svg|max:6144',
            'order' => 'nullable|integer',
        ];

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

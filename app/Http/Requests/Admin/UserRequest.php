<?php

namespace App\Http\Requests\Admin;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Contracts\Validation\Validator;
use Illuminate\Validation\Rule;

class UserRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        $rules = [
            'name'          => 'required|string|max:255',
            'email'         => 'required|email|unique:users,email',
            'phone'         => 'required|string|unique:users,phone',
            'password'      => 'required|string|min:8',
            'country_code'  => 'required|string|max:10',
            'country_name'  => 'required|string|max:255',
            'role'          => 'required|string|in:user,vendor,guest,admin',
            'profile_image' => 'required|image|mimes:jpeg,png,jpg|max:2048',
            'gender'        => 'required|string|in:male,female,other',
            'is_enabled'    => 'required|boolean',
        ];

        if ($this->isMethod('POST')) {
            $rules['name']          = 'nullable|string|max:255';
            $rules['email']         = 'nullable|email';
            $rules['phone']         = 'nullable|string';
            $rules['country_code']  = 'nullable|string|max:10';
            $rules['country_name']  = 'nullable|string|max:255';
            $rules['role']          = 'nullable|string|in:user,vendor,guest,admin';
            $rules['profile_image'] = 'nullable|image|mimes:jpeg,png,jpg|max:2048';
            $rules['gender']        = 'nullable|string|in:male,female,other';
            $rules['is_enabled']    = 'nullable|boolean';
        }

        return $rules;
    }

    protected function failedValidation(Validator $validator)
    {
        failedValidation($validator);
    }
}

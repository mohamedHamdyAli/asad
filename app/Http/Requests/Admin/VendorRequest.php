<?php

namespace App\Http\Requests\Admin;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Contracts\Validation\Validator;
use Illuminate\Http\Request;

class VendorRequest extends FormRequest
{

    public function __construct(Request $request)
    {
       $request->merge([
            'is_enabled' => 1,
            'role' => 'vendor',
        ]);
    }

    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {


        if ($this->route()->getName() === 'vendors.store') {
            $rules = [
                'name'          => 'required|string|max:255',
                'email'         => 'required|email|unique:users,email',
                'phone'         => 'required|string|unique:users,phone',
                'password'      => 'required|string|min:8',
                'country_code'  => 'required|string|max:10',
                'country_name'  => 'required|string|max:255',
                'role'          => 'required|string|in:vendor',
                'profile_image' => 'required|image|mimes:jpeg,png,jpg,svg|max:2048',
                'gender'        => 'required|string|in:male,female,other',
                'is_enabled'    => 'required|boolean',
            ];
        } else {
            $rules = [
                'name'          => 'nullable|string|max:255',
                'email'         => 'nullable|email|unique:users,email',
                'phone'         => 'nullable|string|unique:users,phone',
                'password'      => 'nullable|string|min:8',
                'country_code'  => 'nullable|string|max:10',
                'country_name'  => 'nullable|string|max:255',
                'role'          => 'nullable|string|in:vendor',
                'profile_image' => 'nullable|image|mimes:jpeg,png,jpg,svg|max:2048',
                'gender'        => 'nullable|string|in:male,female,other',
                'is_enabled'    => 'nullable|boolean',
            ];
        }

        return $rules;
    }

    protected function failedValidation(Validator $validator)
    {
        failedValidation($validator);
    }
}

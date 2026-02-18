<?php

namespace App\Http\Requests\Admin;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Contracts\Validation\Validator;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use Illuminate\Validation\Rule;


class VendorRequest extends FormRequest
{

    public function __construct(Request $request)
    {
       $request->merge([
            // 'is_enabled' => 1,
            'role' => 'project manager',
        ]);
    }

    public function authorize(): bool
    {
        return true;
    }


public function rules(): array
{
    $id = $this->route('vendor') ?? $this->route('id');

    if (Route::is('vendors.store')) {
        return [
            'name' => 'required|string|max:255',
            'email' => 'required|email|unique:users,email',
            'phone' => 'required|string|unique:users,phone',
            'password' => 'required|string|min:8',
            'country_code' => 'required|string|max:10',
            'country_name' => 'required|string|max:255',
            'role' => 'required|string|in:project manager',
            'profile_image' => 'required|image|mimes:jpeg,png,jpg,svg|max:2048',
            'gender' => 'required|string|in:male,female,other',
            'is_enabled' => 'required|boolean',
        ];
    }

    // UPDATE
    return [
        'name' => 'nullable|string|max:255',

        'email' => [
            'nullable',
            'email',
            Rule::unique('users', 'email')->ignore($id),
        ],

        'phone' => [
            'nullable',
            'string',
            Rule::unique('users', 'phone')->ignore($id),
        ],

        'password' => 'nullable|string|min:8',
        'country_code' => 'nullable|string|max:10',
        'country_name' => 'nullable|string|max:255',
        'role' => 'nullable|string|in:project manager',
        'profile_image' => 'nullable|image|mimes:jpeg,png,jpg,svg|max:2048',
        'gender' => 'nullable|string|in:male,female,other',
        'is_enabled' => 'nullable|boolean',
    ];
}


    protected function failedValidation(Validator $validator)
    {
        failedValidation($validator);
    }
}

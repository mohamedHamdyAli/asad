<?php

namespace App\Http\Requests\Admin;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Contracts\Validation\Validator;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use Illuminate\Validation\Rule;



class UserRequest extends FormRequest
{
    public function __construct(Request $request)
    {

        $request->merge([
            'role' => 'user',
        ]);
    }
    public function authorize(): bool
    {
        return true;
    }

  public function rules(): array
{
    $userId = $this->route('id');

    if (Route::is('users.store')) {
        return [
            'name' => 'required|string|max:255',
            'email' => 'required|email|unique:users,email',
            'phone' => 'required|string|unique:users,phone',
            'password' => 'required|string|min:6|regex:/^(?=.*\d).+$/',
            'country_code' => 'required|string|max:10',
            'country_name' => 'required|string|max:255',
            'gender' => 'required|in:male,female,other',
            'profile_image' => 'required|image|mimes:jpeg,png,jpg|max:2048',
            'is_enabled' => 'required|boolean',
        ];
    }

    return [
        'name' => 'nullable|string|max:255',
        'email' => [
            'nullable',
            'email',
            Rule::unique('users', 'email')->ignore($userId),
        ],
        'phone' => [
            'nullable',
            Rule::unique('users', 'phone')->ignore($userId),
        ],
        'password' => 'nullable|string|min:6|regex:/^(?=.*\d).+$/',
        'country_code' => 'nullable|string|max:10',
        'country_name' => 'nullable|string|max:255',
        'gender' => 'nullable|in:male,female,other',
        'profile_image' => 'nullable|image|mimes:jpeg,png,jpg|max:2048',
        'is_enabled' => 'nullable|boolean',
    ];
}


    protected function failedValidation(Validator $validator)
    {
        failedValidation($validator);
    }
}

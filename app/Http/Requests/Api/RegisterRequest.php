<?php

namespace App\Http\Requests\Api;
use Illuminate\Http\Request;

class RegisterRequest extends BaseApiRequest
{
    public function __construct(Request $request)
    {

        $request->merge([
            'is_enabled' => 1,
            'role' => 'guest',
        ]);
    }
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'name' => 'required|string|min:2|max:50',
            'email' => 'required|email|unique:users,email|email:filter',
            'phone' => 'required|unique:users,phone|regex:/^[0-9+\-\s]{7,15}$/',
            'country_code' => 'required|regex:/^\+\d{1,4}$/',
            'country_name' => 'required|string|max:50',
            'password' => 'required|min:6|max:20',
            'is_enabled' => 'required|in:1,0',
            'role' => 'required|in:guest,user,vendor,admin',
        ];
    }
}

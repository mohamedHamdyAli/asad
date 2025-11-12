<?php

namespace App\Http\Requests\Api;

class UpdateProfileRequest extends BaseApiRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        $userId = userAuth()->id;

        return [    
            'name' => 'nullable|string|max:191',
            'email' => "nullable|email|unique:users,email,$userId",
            'phone' => "nullable|unique:users,phone,$userId",
            'profile_image' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
        ];
    }
}

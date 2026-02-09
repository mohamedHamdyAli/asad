<?php

namespace App\Http\Requests\Api;


class VerifyOtpRequest extends BaseApiRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'email' => 'required|email|exists:users,email',
            'otp' => 'required|size:4',
            
        ];
    }
    
    public function messages(): array
    {
        return [
            'email.exists' => 'No account found with this email address.',
            'otp.size' => 'OTP must be 4 digits.',
        ];
    }
}

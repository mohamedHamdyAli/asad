<?php

namespace App\services\User;

use App\Http\Resources\RegisterResource;
use App\Models\User;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Auth\Events\PasswordReset;
use Illuminate\Contracts\Auth\PasswordBroker;
use Illuminate\Support\Facades\Crypt;
use Illuminate\Contracts\Encryption\DecryptException;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Password;
use Illuminate\Support\Str;

class UserService
{
    protected $passwordBroker;
    public function __construct(PasswordBroker $passwordBroker)
    {
        $this->passwordBroker = $passwordBroker;
    }

    public function register(array $data)
    {
        return DB::transaction(function () use ($data) {
            $user = User::create($data);
            $user->assignRole('guest');
            $user->token = 'Bearer ' . $user->createToken("{$user->role}:{$user->id}")->accessToken;
            return successReturnData(new RegisterResource($user), 'Data Fetched Successfully');
        });
    }
    public function login(array $data)
    {
        return DB::transaction(function () use ($data) {
            $credentials = [
                'phone' => $data['phone'],
                'password' => $data['password'],
            ];

            if (!Auth::attempt($credentials)) {
                return failReturnMsg('Invalid credentials');
            }

            $user = Auth::user();

            if (!$user->is_enabled) {
                Auth::logout();
                return failReturnMsg('Your account is disabled');
            }

            if (!in_array($user->role, ['user', 'guest'])) {
                Auth::logout();
                return failReturnMsg('Unauthorized role');
            }

            $user->tokens()->delete();

            $token = $user->createToken("{$user->role}:{$user->id}")->accessToken;
            $user->token = "Bearer $token";

            return successReturnData(new RegisterResource($user), 'Data fetched successfully');
        });
    }

    public function sendResetLink($data)
    {
        $user = User::where('email', $data['email'])->first();

        if (!$user) {
            return failReturnMsg('No user found with this email');
        }

        $token = $this->passwordBroker->createToken($user);
        $encryptedToken = Crypt::encryptString($token);

        return successReturnData($encryptedToken, 'Password reset link sent successfully');
    }

    public function resetPassword(array $data)
    {
        try {
            $data['token'] = Crypt::decryptString($data['token']);
        } catch (DecryptException $e) {
            return failReturnMsg('The provided token is invalid.');
        }

        $status = Password::reset(
            $data,
            function ($user, $password) {
                // ✅ Check if the new password is the same as the old one
                if (Hash::check($password, $user->password)) {
                    // لو نفس الباسورد القديم نوقف العملية
                    failReturnMsg('The new password cannot be the same as the old password.');
                }

                $user->forceFill([
                    'password' => Hash::make($password),
                ])->setRememberToken(Str::random(60));

                $user->save();

                event(new PasswordReset($user));
            }
        );

        return match ($status) {
            Password::PASSWORD_RESET => returnSuccessMsg('Password reset successful'),
            Password::INVALID_TOKEN => failReturnMsg('This password reset token is invalid or has expired.'),
            Password::INVALID_USER => failReturnMsg('We can\'t find a user with that email address.'),
            default => failReturnMsg(
                $status instanceof \Exception
                ? $status->getMessage()
                : 'Failed to reset password.'
            ),
        };
    }




}

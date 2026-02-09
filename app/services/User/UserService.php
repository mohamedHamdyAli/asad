<?php

namespace App\services\User;

use App\Models\User;
use App\Mail\OtpMail;
use App\Models\Setting;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;
use App\Http\Resources\UserResource;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Crypt;
use App\Http\Resources\SettingResource;
use App\Http\Resources\RegisterResource;
use Illuminate\Support\Facades\Password;
use Illuminate\Auth\Events\PasswordReset;
use Illuminate\Contracts\Auth\PasswordBroker;
use Illuminate\Contracts\Encryption\DecryptException;

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
            $otp = generateOtp();

            $data['otp'] = $otp;
            $data['otp_expiry'] = now()->addMinutes(5);

            $user = User::create($data);
            $user->assignRole('guest');

            try {
                Mail::to($user->email)->send(new OtpMail($otp, $user->name));
            } catch (\Exception $e) {
                Log::error('Failed to send OTP email: ' . $e->getMessage());
            }

            return returnSuccessMsg('OTP sent to your email. Please verify to complete registration.');
        });
    }

    public function verifyOtp(array $data)
    {
        $user = User::where('email', $data['email'])->first();

        if (!$user) {
            return failReturnMsg('User not found');
        }

        if ($user->email_verified_at) {
            return failReturnMsg('Email already verified');
        }

        if ($user->otp !== $data['otp']) {
            return failReturnMsg('Invalid OTP');
        }

        if (now()->greaterThan($user->otp_expiry)) {
            return failReturnMsg('OTP has expired. Please request a new one.');
        }

        $user->update([
            'otp' => null,
            'otp_expiry' => null,
            'email_verified_at' => now(),
        ]);

        $role = $user->getRoleNames()->first();
        $user->token = 'Bearer ' . $user->createToken("{$role}:{$user->id}")->accessToken;

        return successReturnData(new RegisterResource($user), 'Email verified successfully');
    }

    public function resendOtp(array $data)
    {
        $user = User::where('email', $data['email'])->first();

        if (!$user) {
            return failReturnMsg('User not found');
        }

        if ($user->email_verified_at) {
            return failReturnMsg('Email already verified');
        }

        $otp = generateOtp();

        $user->update([
            'otp' => $otp,
            'otp_expiry' => now()->addMinutes(5),
        ]);

        Mail::to($user->email)->send(new OtpMail($otp, $user->name));

        return returnSuccessMsg('OTP resent to your email');
    }
    public function login(array $data)
    {
        return DB::transaction(function () use ($data) {

            $credentials = [
                'phone'    => $data['phone'],
                'password' => $data['password'],
            ];

            if (!Auth::attempt($credentials)) {
                return failReturnMsg('Invalid credentials');
            }

            /** @var \App\Models\User $user */
            $user = Auth::user();

            // if (!$user->email_verified_at) {
            //     Auth::logout();
            //     return failReturnMsg('Please verify your email first');
            // }

            if (!$user->is_enabled) {
                Auth::logout();
                return failReturnMsg('Your account is disabled');
            }

            if (! $user->hasAnyRole(['user', 'guest'])) {
                Auth::logout();
                return failReturnMsg('Unauthorized role');
            }


            $role = $user->getRoleNames()->first();

            $token = $user->createToken(
                "{$role}:{$user->id}"
            )->accessToken;

            $user->token = "Bearer {$token}";


            return successReturnData(
                new RegisterResource($user),
                'Data fetched successfully'
            );
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

        $user = User::where('email', $data['email'])->first();
        if (!$user) {
            return failReturnMsg('No user found with this email');
        }

        if (Hash::check($data['password'], $user->password)) {
            return failReturnMsg('The new password cannot be the same as the old password.');
        }
        $status = Password::reset(
            $data,
            function ($user, $password) {
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

    public function UserProfile()
    {
        $user = userOrGuestAuth();
        if (!$user) {
            return failReturnMsg('User not authenticated');
        }
        return successReturnData(new UserResource($user), 'profile fetched successfully');
    }

    public function updateProfile($data)
    {
        $user = userOrGuestAuth();

        return DB::transaction(function () use ($user, $data) {
            $filteredData = array_filter($data, function ($value) {
                return !is_null($value);
            });
            if (isset($filteredData['profile_image'])) {
                $filteredData['profile_image'] = uploadOrUpdateImage(
                    $filteredData['profile_image'],
                    'users/profile_images/',
                    $user->profile_image
                );
            }

            $user->update($filteredData);
            return successReturnData(new UserResource($user), 'Profile updated successfully');
        });
    }


    public function setting()
    {
        return successReturnData(new SettingResource(Setting::all()), 'data sent successfully');
    }
}

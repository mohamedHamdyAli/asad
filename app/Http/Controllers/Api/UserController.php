<?php

namespace App\Http\Controllers\Api;

use App\services\User\UserService;
use App\Http\Controllers\Controller;
use App\Http\Requests\Api\LoginRequest;
use App\Http\Requests\Api\RegisterRequest;
use App\Http\Requests\Api\ResetPasswordRequest;
use App\Http\Requests\Api\UpdateProfileRequest;
use App\Http\Requests\Api\ForgotPasswordRequest;

class UserController extends Controller
{
    private UserService $userService;
    public function __construct(UserService $userService)
    {
        $this->userService = $userService;
    }
    public function register(RegisterRequest $request)
    {
        return $this->userService->register($request->validated());
    }
    public function login(LoginRequest $request)
    {
        return $this->userService->login($request->validated());
    }
    public function sendResetLinkEmail(ForgotPasswordRequest $request)
    {
        return $this->userService->sendResetLink($request->validated());
    }
    public function resetPassword(ResetPasswordRequest $request)
    {
        return $this->userService->resetPassword($request->validated());
    }
    public function profile()
    {
        return $this->userService->UserProfile();
    }

    public function updateProfile(UpdateProfileRequest $request)
    {
        return $this->userService->updateProfile($request->validated());
    }

    public function setting()
    {
        return $this->userService->setting();
    }
}

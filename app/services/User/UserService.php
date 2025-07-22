<?php

namespace App\services\User;

use App\Http\Resources\RegisterResource;
use App\Models\Language;
use App\Models\User;
use App\Services\FileService;
use Illuminate\Support\Facades\DB;

class UserService
{
    private string $uploadFolder;

    public function __construct()
    {
        $this->uploadFolder = "user";
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

}

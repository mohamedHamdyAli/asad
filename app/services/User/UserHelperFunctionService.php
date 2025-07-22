<?php

namespace App\Services\User;

use App\Models\User;
use App\Services\FileService;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;

class UserHelperFunctionService
{
    private string $uploadFolder;

    public function __construct()
    {
        $this->uploadFolder = "users";
    }


    public function getUserData($id = null)
    {

        return $id ? User::findOrFail($id) : User::all();
    }

       public function getUserById(int $id)
    {
        return User::find($id);
    }
    public function createUser($request): bool
    {
        return DB::transaction(function () use ($request) {
            if (!empty($request['profile_image'])) {
                $request['profile_image'] = FileService::upload($request['profile_image'], $this->uploadFolder);
            }

            $request['password'] = Hash::make($request['password']);

            User::create($request);
            return true;
        });
    }

    public function updateUserData(array $request, int $id): bool
    {
        return DB::transaction(function () use ($request, $id) {
            $user = User::findOrFail($id);

            if (!empty($request['profile_image'])) {
                $request['profile_image'] = FileService::replace(
                    $request['profile_image'],
                    $this->uploadFolder,
                    $user->getRawOriginal('profile_image')
                );
            }

            if (!empty($request['password'])) {
                $request['password'] = Hash::make($request['password']);
            }

            $user->update($request);
            return true;
        });
    }

    public function deleteUser(int $id): bool
    {
        return DB::transaction(function () use ($id) {
            $user = User::findOrFail($id);

            if ($user->profile_image) {
                FileService::delete($user->getRawOriginal('profile_image'));
            }

            $user->delete();

            return true;
        });
    }
}

<?php

namespace App\Services\User;

use App\Models\User;
use App\services\FileService;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;

class UserHelperFunctionService
{
    private string $uploadFolder;

    public function __construct()
    {
        $this->uploadFolder = 'users';
    }

// public function getUserData($id = null)
// {
//     if ($id) {
//         return User::with('roles')->where('id', $id)->first();
//     }

//     return User::with('roles')->get();
// }
public function getUserData($id = null)
{
    $query = User::query()->with('roles')->role('user');

    if ($id) {
        return $query->find($id);
    }

    return $query->get();
}


    public function createUser(array $request): bool
    {
        return DB::transaction(function () use ($request) {

            if (!empty($request['profile_image'])) {
                $request['profile_image'] = FileService::upload(
                    $request['profile_image'],
                    $this->uploadFolder
                );
            }

            $user = User::create($request);

            $user->assignRole('user');

            return true;
        });
    }

    public function updateUserData(array $request, int $id): bool
    {
        return DB::transaction(function () use ($request, $id) {

            $user = User::find($id);

            if (!$user) {
                return false;
            }

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

            $user = User::find($id);

            if (!$user) {
                return false;
            }

            if ($user->profile_image) {
                FileService::delete(
                    $user->getRawOriginal('profile_image')
                );
            }

            $user->delete();

            return true;
        });
    }
}

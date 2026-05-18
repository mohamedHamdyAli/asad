<?php

namespace App\Services\User;

use App\Models\User;
use App\services\FileService;
use App\Trait\notifications\NotificationTrait;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Log;

class UserHelperFunctionService
{
    use NotificationTrait;

    private string $uploadFolder;

    private array $notifiableFields = [
        'name'         => 'Name',
        'email'        => 'Email',
        'phone'        => 'Phone',
        'country_code' => 'Country code',
        'country_name' => 'Country',
        'gender'       => 'Gender',
        'profile_image' => 'Profile image',
        'password'     => 'Password',
        'is_enabled'   => 'Account status',
    ];

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
        $user = User::find($id);

        if (!$user) {
            return false;
        }

        $originalValues = [];
        foreach (array_keys($this->notifiableFields) as $key) {
            $originalValues[$key] = $user->getRawOriginal($key);
        }

        $updated = DB::transaction(function () use (&$request, $user) {

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

            return $user->fresh();
        });

        if ($updated) {
            $this->notifyUserOfUpdate($updated, $request, $originalValues);
        }

        return true;
    }

    private function notifyUserOfUpdate(User $user, array $request, array $originalValues): void
    {
        $changedFields = [];
        foreach ($this->notifiableFields as $key => $label) {
            if (!array_key_exists($key, $request)) {
                continue;
            }

            $newValue = $request[$key];
            $oldValue = $originalValues[$key] ?? null;

            if ($key === 'password') {
                if (!empty($request['password'])) {
                    $changedFields[] = $label;
                }
                continue;
            }

            if ($newValue === null || $newValue === '') {
                continue;
            }

            if ($key === 'is_enabled') {
                if ((bool) $newValue !== (bool) $oldValue) {
                    $changedFields[] = $label;
                }
                continue;
            }

            if ((string) $newValue !== (string) $oldValue) {
                $changedFields[] = $label;
            }
        }

        if (empty($changedFields)) {
            return;
        }

        $message = 'Your account information was updated by an administrator. Updated fields: '
            . implode(', ', $changedFields) . '.';

        try {
            $this->appNotification([
                'title'   => 'Account information updated',
                'message' => $message,
                'user'    => $user,
            ]);
        } catch (\Throwable $e) {
            Log::error('User update notification failed: ' . $e->getMessage());
        }
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

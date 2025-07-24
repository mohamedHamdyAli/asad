<?php

namespace App\Services\Vendor;

use App\Models\User;
use App\Services\FileService;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;


class VendorHelperFunctionService
{
    private string $uploadFolder;

    public function __construct()
    {
        $this->uploadFolder = "vendors";
    }
    public function getVendorData($id = null)
    {
        return $id ? User::where(['id' => $id, 'role' => 'vendor'])->first() : User::where('role', 'vendor')->get();
    }

    public function createVendor(array $request): bool
    {
        return DB::transaction(function () use ($request) {
            if (!empty($request['profile_image'])) {
                $request['profile_image'] = FileService::upload($request['profile_image'], $this->uploadFolder);
            }

            $vendor = User::create($request);
            $vendor->assignRole('vendor');

            return true;
        });
    }

    public function updateVendorData(array $request, int $id): bool
    {
        return DB::transaction(function () use ($request, $id) {
            $vendor = User::where(['id' => $id, 'role' => 'vendor'])->first();
            if (!$vendor) {
                return false;
            }

            if (!empty($request['profile_image'])) {
                $request['profile_image'] = FileService::replace(
                    $request['profile_image'],
                    $this->uploadFolder,
                    $vendor->getRawOriginal('profile_image')
                );
            }

            if (!empty($request['password'])) {
                $request['password'] = Hash::make($request['password']);
            } else {
                unset($request['password']);
            }

            $vendor->update($request);
            return true;
        });
    }

    public function deleteVendor(int $id): bool
    {
        return DB::transaction(function () use ($id) {
            $vendor = User::where(['id' => $id, 'role' => 'vendor'])->first();
            if (!$vendor) {
                return false;
            }

            if ($vendor->profile_image) {
                FileService::delete($vendor->getRawOriginal('profile_image'));
            }

            $vendor->delete();
            return true;
        });
    }
}

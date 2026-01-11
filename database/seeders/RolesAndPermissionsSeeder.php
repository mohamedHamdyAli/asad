<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;
use App\Models\User;
use Illuminate\Support\Facades\Hash;
use Spatie\Permission\PermissionRegistrar;

class RolesAndPermissionsSeeder extends Seeder
{
    public function run(): void
    {
        // Clear cached permissions
        app(PermissionRegistrar::class)->forgetCachedPermissions();

        /*
        |--------------------------------------------------------------------------
        | Permissions
        |--------------------------------------------------------------------------
        */
        $permissions = [
            // Dashboard
            'view dashboard',

            // Users
            'view users',
            'create users',
            'edit users',
            'delete users',

            // Roles & permissions
            'view roles',
            'create roles',
            'edit roles',
            'delete roles',

            // Languages
            'view languages',
            'create languages',
            'edit languages',
            'delete languages',

            // Project managers
            'manage project managers',

            // Reports
            'view reports',
        ];

        foreach ($permissions as $permission) {
            Permission::firstOrCreate([
                'name' => $permission,
                'guard_name' => 'web',
            ]);
        }


        $roles = [
            'admin' => [
                'view dashboard',

                'view users',
                'create users',
                'edit users',
                'delete users',

                'view roles',
                'create roles',
                'edit roles',
                'delete roles',

                'view languages',
                'create languages',
                'edit languages',
                'delete languages',

                'manage project managers',
                'view reports',
            ],

            'user' => [
                'view dashboard',
                'view users',
                'view languages',
                'view reports',
            ],

            'vendor' => [
                'view dashboard',
            ],

            'guest' => [
                'view dashboard',
            ],
        ];

        foreach ($roles as $roleName => $permissions) {
            $role = Role::firstOrCreate([
                'name' => $roleName,
                'guard_name' => 'web',
            ]);

            $role->syncPermissions($permissions);
        }


        $admin = User::firstOrCreate(
            ['email' => 'admin@dashboard.com'],
            [
                'name' => 'Admin',
                'password' => Hash::make('password'),
            ]
        );

        $admin->syncRoles(['admin']);

        $this->command->info('Roles & permissions seeded successfully');
        $this->command->info('Admin login: admin@dashboard.com / password');
    }
}

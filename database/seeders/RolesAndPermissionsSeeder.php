<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;
use App\Models\User;
use Illuminate\Support\Facades\Hash;

class RolesAndPermissionsSeeder extends Seeder
{
    public function run(): void
    {
        app()[\Spatie\Permission\PermissionRegistrar::class]->forgetCachedPermissions();

        $permissions = [
            // Dashboard
            'view dashboard',

            // Users
            'view users',
            'create users',
            'edit users',
            'delete users',

            // Roles
            'view roles',
            'create roles',
            'edit roles',
            'delete roles',

            // Languages
            'view languages',
            'create languages',
            'edit languages',
            'delete languages',

            'manage project managers',
            'view reports',
        ];

        foreach ($permissions as $perm) {
            Permission::firstOrCreate(['name' => $perm, 'guard_name' => 'web']);
        }

   
        $roles = [
            'super-admin' => Permission::all()->pluck('name')->toArray(), // all permissions
            'admin'       => [
                'view dashboard',
                'view users',
                'create users',
                'edit users',
                'view roles',
                'view languages',
                'edit languages',
            ],
            'viewer'      => ['view dashboard', 'view users', 'view roles'],
        ];

        foreach ($roles as $roleName => $perms) {
            $role = Role::firstOrCreate(['name' => $roleName, 'guard_name' => 'web']);
            $role->syncPermissions($perms);
        }

    
        $admin = User::firstOrCreate(
            ['email' => 'admin@dashboard.com'],
            [
                'name' => 'Super Admin',
                'password' => Hash::make('password'),
            ]
        );

        $admin->assignRole('super-admin');

        $this->command->info(' Roles, permissions, and admin user seeded successfully!');
        $this->command->info('   Login with: admin@example.com / password');
    }
}

<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use Spatie\Permission\PermissionRegistrar;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;
use App\Models\User;

class RolesAndPermissionsSeeder extends Seeder
{
    public function run(): void
    {
        // Clear cached permissions
        app(PermissionRegistrar::class)->forgetCachedPermissions();

        /*
        |--------------------------------------------------------------------------
        | Define modules & permissions
        |--------------------------------------------------------------------------
        | Convention:
        | - CRUD: module.view, module.create, module.update, module.delete
        | - Extra actions: module.action_name
        */
        $crudModules = [
            // Admin CMS modules
            'languages',
            'intro',
            'banners',
            'users',
            'PMS',
            'units',
            'folders',
            'unit_docs',
            'unit_gallery',
            'unit_drawings',
            'unit_reports',
            'unit_timeline',
            'unit_phases',
            'unit_contractors',
            'unit_live_cameras',
            'contractors',
            'contact_infos',
            'consultants',
            'unit_consultants',
            'type_of_buildings',
            'type_of_prices',
            'unit_quotes',
            'unit_quote_responses',
            'unit_phase_notes',
            'unit_issues',
            'unit_payments',
            'notifications',

            // RBAC modules
            'roles',
            'permissions',
        ];

        $extraPermissions = [
            // Roles extra
            'roles.sync_permissions',

            // Users extra
            'users.assign_role',
            'users.assign_permission',

            // Unit Payments sub-features (non-CRUD endpoints)
            'unit_payment_installments.view',        // show list of installments by unitPaymentId
            'unit_payment_installments.create',
            'unit_payment_installments.update',
            'unit_payment_installments.delete',

            'unit_payment_invoices.view',            // get invoices for installment
            'unit_payment_installments.update_status', // installments/{installment}/status
            'unit_payment_invoices.confirm',         // invoices/confirm

            'unit_payment_logs.view',                // logs by unit or installment
        ];

        // Build full permission list
        $permissions = [];

        foreach ($crudModules as $module) {
            $permissions[] = "{$module}.view";
            $permissions[] = "{$module}.create";
            $permissions[] = "{$module}.update";
            $permissions[] = "{$module}.delete";
        }

        $permissions = array_merge($permissions, $extraPermissions);
        $permissions = array_values(array_unique($permissions)); // ensure no duplicates

        // Seed permissions
        foreach ($permissions as $permission) {
            Permission::firstOrCreate([
                'name' => $permission,
                'guard_name' => 'web',
            ]);
        }

        /*
        |--------------------------------------------------------------------------
        | Roles
        |--------------------------------------------------------------------------
        | Admin gets everything automatically.
        | You can adjust other roles as you like.
        */
        $roles = [
            'admin' => Permission::where('guard_name', 'web')->pluck('name')->toArray(),

            // Limited dashboard/operator user
            // 'user' => [
            //     'languages.view',
            //     'units.view',
            //     'unit_docs.view',
            //     'unit_gallery.view',
            //     'unit_drawings.view',
            //     'unit_reports.view',
            //     'unit_timeline.view',
            //     'unit_phases.view',
            //     'unit_issues.view',
            //     'unit_quotes.view',
            //     'unit_quote_responses.view',
            //     'notifications.view',
            // ],

            // Example: can manage units and unit content but not RBAC
            'project manager' => [
                'units.view', 'units.create', 'units.update',
                'unit_docs.view', 'unit_docs.create', 'unit_docs.update', 'unit_docs.delete',
                'unit_gallery.view', 'unit_gallery.create', 'unit_gallery.update', 'unit_gallery.delete',
                'unit_drawings.view', 'unit_drawings.create', 'unit_drawings.update', 'unit_drawings.delete',
                'unit_reports.view', 'unit_reports.create', 'unit_reports.update', 'unit_reports.delete',
                'unit_timeline.view', 'unit_timeline.create', 'unit_timeline.update', 'unit_timeline.delete',
                'unit_phases.view', 'unit_phases.create', 'unit_phases.update', 'unit_phases.delete',
                'unit_contractors.view', 'unit_contractors.create', 'unit_contractors.update', 'unit_contractors.delete',
                'unit_consultants.view', 'unit_consultants.create', 'unit_consultants.update', 'unit_consultants.delete',
                'unit_live_cameras.view', 'unit_live_cameras.create', 'unit_live_cameras.update', 'unit_live_cameras.delete',

                'unit_issues.view', 'unit_issues.update',
                'unit_phase_notes.view', 'unit_phase_notes.create', 'unit_phase_notes.update', 'unit_phase_notes.delete',
            ],

            // Consultant: same permissions as project manager
            'consultant' => [
                'units.view', 'units.create', 'units.update',
                'unit_docs.view', 'unit_docs.create', 'unit_docs.update', 'unit_docs.delete',
                'unit_gallery.view', 'unit_gallery.create', 'unit_gallery.update', 'unit_gallery.delete',
                'unit_drawings.view', 'unit_drawings.create', 'unit_drawings.update', 'unit_drawings.delete',
                'unit_reports.view', 'unit_reports.create', 'unit_reports.update', 'unit_reports.delete',
                'unit_timeline.view', 'unit_timeline.create', 'unit_timeline.update', 'unit_timeline.delete',
                'unit_phases.view', 'unit_phases.create', 'unit_phases.update', 'unit_phases.delete',
                'unit_contractors.view', 'unit_contractors.create', 'unit_contractors.update', 'unit_contractors.delete',
                'unit_consultants.view', 'unit_consultants.create', 'unit_consultants.update', 'unit_consultants.delete',
                'unit_live_cameras.view', 'unit_live_cameras.create', 'unit_live_cameras.update', 'unit_live_cameras.delete',

                'unit_issues.view', 'unit_issues.update',
                'unit_phase_notes.view', 'unit_phase_notes.create', 'unit_phase_notes.update', 'unit_phase_notes.delete',

                // Consultant management
                'consultants.view', 'consultants.create', 'consultants.update', 'consultants.delete',

                // Contractor management
                'contractors.view', 'contractors.create', 'contractors.update', 'contractors.delete',
            ],

            // 'guest' => [
            //     // keep empty or very limited (admin panel usually none)
            // ],

            'vendor' => [],
        ];

        // Create roles & sync permissions
        foreach ($roles as $roleName => $rolePermissions) {
            $role = Role::firstOrCreate([
                'name' => $roleName,
                'guard_name' => 'web',
            ]);

            $role->syncPermissions($rolePermissions);
        }

        /*
        |--------------------------------------------------------------------------
        | Admin User
        |--------------------------------------------------------------------------
        */
        $admin = User::firstOrCreate(
            ['email' => 'admin@dashboard.com'],
            [
                'name' => 'Admin',
                'password' => Hash::make('password'),
            ]
        );

        $admin->syncRoles(['admin']);
    }
}

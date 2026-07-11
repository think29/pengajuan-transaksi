<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\PermissionRegistrar;

class RolePermissionSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Reset cache permission
        app()[PermissionRegistrar::class]->forgetCachedPermissions();

        /*
        |--------------------------------------------------------------------------
        | Permissions
        |--------------------------------------------------------------------------
        */

        $permissions = [

            // Category
            'category.view',
            'category.create',
            'category.update',
            'category.delete',

            // Budget
            'budget.view',
            'budget.create',
            'budget.update',
            'budget.delete',

            // Submission
            'submission.view',
            'submission.create',
            'submission.update',
            'submission.delete',

            // Approval
            'approval.process',

            // Payment
            'payment.process',

            // User
            'user.manage',

            // Role
            'role.manage',
        ];

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
        */

        $admin = Role::firstOrCreate([
            'name' => 'Admin',
            'guard_name' => 'web',
        ]);

        $staff = Role::firstOrCreate([
            'name' => 'Staff',
            'guard_name' => 'web',
        ]);

        $spv = Role::firstOrCreate([
            'name' => 'SPV',
            'guard_name' => 'web',
        ]);

        $manager = Role::firstOrCreate([
            'name' => 'Manager',
            'guard_name' => 'web',
        ]);

        $director = Role::firstOrCreate([
            'name' => 'Director',
            'guard_name' => 'web',
        ]);

        $finance = Role::firstOrCreate([
            'name' => 'Finance',
            'guard_name' => 'web',
        ]);

        /*
        |--------------------------------------------------------------------------
        | Assign Permissions
        |--------------------------------------------------------------------------
        */

        // Admin
        $admin->givePermissionTo(Permission::all());

        // Staff
        $staff->givePermissionTo([
            'submission.view',
            'submission.create',
            'submission.update',
        ]);

        // SPV
        $spv->givePermissionTo([
            'submission.view',
            'approval.process',
        ]);

        // Manager
        $manager->givePermissionTo([
            'submission.view',
            'approval.process',
        ]);

        // Director
        $director->givePermissionTo([
            'submission.view',
            'approval.process',
        ]);

        // Finance
        $finance->givePermissionTo([
            'submission.view',
            'payment.process',
        ]);
    }
}
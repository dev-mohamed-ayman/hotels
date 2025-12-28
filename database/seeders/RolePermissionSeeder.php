<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;

class RolePermissionSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Reset cached roles and permissions
        app()[\Spatie\Permission\PermissionRegistrar::class]->forgetCachedPermissions();

        // Create permissions
        $permissions = [
            // Users permissions
            'view users',
            'create users',
            'edit users',
            'delete users',

            // Roles permissions
            'view roles',
            'create roles',
            'edit roles',
            'delete roles',

            // Permissions management
            'view permissions',
            'create permissions',
            'edit permissions',
            'delete permissions',

            // Dashboard permissions
            'view dashboard',

            // Currencies permissions
            'view currencies',
            'create currencies',
            'edit currencies',
            'delete currencies',

            // Hotels permissions
            'view hotels',
            'create hotels',
            'edit hotels',
            'delete hotels',

            // Customers permissions
            'view customers',
            'create customers',
            'edit customers',
            'delete customers',

            // Bookings permissions
            'view bookings',
            'create bookings',
            'edit bookings',
            'delete bookings',
            'export bookings',
            'view booking margins',
            'view booking guest rates',
        ];

        foreach ($permissions as $permission) {
            Permission::firstOrCreate(['name' => $permission]);
        }

        // Create roles
        $superAdminRole = Role::firstOrCreate(['name' => 'Super Admin']);
        $adminRole = Role::firstOrCreate(['name' => 'Admin']);
        $managerRole = Role::firstOrCreate(['name' => 'Manager']);
        $userRole = Role::firstOrCreate(['name' => 'User']);

        // Assign all permissions to Super Admin
        $superAdminRole->givePermissionTo(Permission::all());

        // Assign permissions to Admin (all except users/roles management)
        $adminRole->givePermissionTo([
            'view dashboard',
            'view currencies',
            'create currencies',
            'edit currencies',
            'delete currencies',
            'view hotels',
            'create hotels',
            'edit hotels',
            'delete hotels',
            'view customers',
            'create customers',
            'edit customers',
            'delete customers',
            'view bookings',
            'create bookings',
            'edit bookings',
            'delete bookings',
            'export bookings',
            'view booking margins',
            'view booking guest rates',
        ]);

        // Assign permissions to Manager (view and create/edit, no delete)
        $managerRole->givePermissionTo([
            'view dashboard',
            'view currencies',
            'view hotels',
            'create hotels',
            'edit hotels',
            'view customers',
            'create customers',
            'edit customers',
            'view bookings',
            'create bookings',
            'edit bookings',
            'export bookings',
            'view booking margins',
        ]);

        // Assign basic permissions to User (view only)
        $userRole->givePermissionTo([
            'view dashboard',
            'view hotels',
            'view customers',
            'view bookings',
            'export bookings',
        ]);

        // Assign Super Admin role to the first admin user
        $adminUser = \App\Models\User::where('email', 'admin@admin.com')->first();
        if ($adminUser) {
            $adminUser->assignRole('Super Admin');
        }
    }
}











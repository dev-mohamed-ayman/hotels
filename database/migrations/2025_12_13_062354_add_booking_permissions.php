<?php

use Illuminate\Database\Migrations\Migration;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        // Create new permissions
        Permission::firstOrCreate(['name' => 'view booking margins']);
        Permission::firstOrCreate(['name' => 'view booking guest rates']);

        // Assign permissions to existing roles
        $superAdminRole = Role::where('name', 'Super Admin')->first();
        $adminRole = Role::where('name', 'Admin')->first();
        $managerRole = Role::where('name', 'Manager')->first();

        if ($superAdminRole) {
            $superAdminRole->givePermissionTo(['view booking margins', 'view booking guest rates']);
        }

        if ($adminRole) {
            $adminRole->givePermissionTo(['view booking margins', 'view booking guest rates']);
        }

        if ($managerRole) {
            $managerRole->givePermissionTo(['view booking margins']);
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Permission::where('name', 'view booking margins')->delete();
        Permission::where('name', 'view booking guest rates')->delete();
    }
};
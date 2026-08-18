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
        $permission = Permission::firstOrCreate([
            'name' => 'export Client',
            'guard_name' => 'web',
        ]);

        // Every role that can already run the Guest export gets the Client export too.
        Role::whereHas('permissions', function ($query) {
            $query->where('name', 'export Guest');
        })->get()->each(function ($role) use ($permission) {
            $role->givePermissionTo($permission);
        });

        app()[\Spatie\Permission\PermissionRegistrar::class]->forgetCachedPermissions();
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Permission::where('name', 'export Client')->where('guard_name', 'web')->delete();

        app()[\Spatie\Permission\PermissionRegistrar::class]->forgetCachedPermissions();
    }
};

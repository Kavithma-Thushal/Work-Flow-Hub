<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;
use Illuminate\Support\Facades\Schema;
use Spatie\Permission\PermissionRegistrar;

class RolePermissionSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Disable foreign key constraints to prevent issues during truncation
        Schema::disableForeignKeyConstraints();
        app()[PermissionRegistrar::class]->forgetCachedPermissions();

        // Truncate roles to avoid duplicates
        Permission::truncate();

        $guard = 'web';

        // Create roles
        Role::firstOrCreate(['name' => 'company', 'guard_name' => $guard]);
        Role::firstOrCreate(['name' => 'employee', 'guard_name' => $guard]);

        // Re-enable foreign key constraints
        Schema::enableForeignKeyConstraints();
    }
}

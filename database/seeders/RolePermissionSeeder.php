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
        Schema::disableForeignKeyConstraints();
        app()[PermissionRegistrar::class]->forgetCachedPermissions();
        Permission::truncate();

        $guard = 'web';

        // Create 'employee-save' permission
        $employeeSave = Permission::updateOrCreate(['name' => 'employee-save', 'guard_name' => $guard]);

        // Create 'Company' role and assign permission
        $company = Role::firstOrCreate(['name' => 'Company']);
        $company->syncPermissions([$employeeSave]);

        // Create 'Employee' role and assign permission
        $employee = Role::firstOrCreate(['name' => 'Employee']);
        $employee->syncPermissions([$employeeSave]);

        Schema::enableForeignKeyConstraints();
    }
}

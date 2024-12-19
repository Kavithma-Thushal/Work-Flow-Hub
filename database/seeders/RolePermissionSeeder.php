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

        $employeeSave = Permission::updateOrCreate(['name' => 'employee-save', 'guard_name' => $guard]);

        $company = Role::firstOrCreate(['name' => 'Company']);
        $company->syncPermissions([$employeeSave]);

        $employee = Role::firstOrCreate(['name' => 'Employee']);

        Schema::enableForeignKeyConstraints();
    }
}

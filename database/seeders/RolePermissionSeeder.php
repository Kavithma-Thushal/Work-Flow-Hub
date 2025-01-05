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

        $guard = 'api';

        // Employee CRUD
        $employeeStore = Permission::updateOrCreate(['name' => 'employee-store', 'guard_name' => $guard]);
        $employeeUpdate = Permission::updateOrCreate(['name' => 'employee-update', 'guard_name' => $guard]);
        $employeeDelete = Permission::updateOrCreate(['name' => 'employee-delete', 'guard_name' => $guard]);
        $employeeGetById = Permission::updateOrCreate(['name' => 'employee-getById', 'guard_name' => $guard]);
        $employeeGetAll = Permission::updateOrCreate(['name' => 'employee-getAll', 'guard_name' => $guard]);

        // Leaves
        $leaveStore = Permission::updateOrCreate(['name' => 'leave-store', 'guard_name' => $guard]);
        $leaveGetById = Permission::updateOrCreate(['name' => 'leave-getById', 'guard_name' => $guard]);
        $leaveGetAll = Permission::updateOrCreate(['name' => 'leave-getAll', 'guard_name' => $guard]);

        // Company
        $company = Role::firstOrCreate(['name' => 'Company']);
        $company->syncPermissions([$employeeStore, $employeeUpdate, $employeeDelete, $employeeGetById, $employeeGetAll, $leaveGetById]);

        // Employee
        $employee = Role::firstOrCreate(['name' => 'Employee']);
        $employee->syncPermissions([/*$employeeGetById, $employeeGetAll,*/ $leaveStore, $leaveGetAll]);

        Schema::enableForeignKeyConstraints();
    }
}

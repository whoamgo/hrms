<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Permission;

class PermissionSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $permissions = [
            // User Management
            ['name' => 'View Users', 'slug' => 'view-users', 'module' => 'User Management', 'description' => 'Can view users list'],
            ['name' => 'Create Users', 'slug' => 'create-users', 'module' => 'User Management', 'description' => 'Can create new users'],
            ['name' => 'Edit Users', 'slug' => 'edit-users', 'module' => 'User Management', 'description' => 'Can edit users'],
            ['name' => 'Delete Users', 'slug' => 'delete-users', 'module' => 'User Management', 'description' => 'Can delete users'],
            
            // Employee Management
            ['name' => 'View Employees', 'slug' => 'view-employees', 'module' => 'Employee Management', 'description' => 'Can view employees'],
            ['name' => 'Create Employees', 'slug' => 'create-employees', 'module' => 'Employee Management', 'description' => 'Can create employees'],
            ['name' => 'Edit Employees', 'slug' => 'edit-employees', 'module' => 'Employee Management', 'description' => 'Can edit employees'],
            ['name' => 'Delete Employees', 'slug' => 'delete-employees', 'module' => 'Employee Management', 'description' => 'Can delete employees'],
            
            // Leave Management
            ['name' => 'View Leaves', 'slug' => 'view-leaves', 'module' => 'Leave Management', 'description' => 'Can view leave requests'],
            ['name' => 'Approve Leaves', 'slug' => 'approve-leaves', 'module' => 'Leave Management', 'description' => 'Can approve leave requests'],
            ['name' => 'Reject Leaves', 'slug' => 'reject-leaves', 'module' => 'Leave Management', 'description' => 'Can reject leave requests'],
            
            // Attendance
            ['name' => 'View Attendance', 'slug' => 'view-attendance', 'module' => 'Attendance', 'description' => 'Can view attendance'],
            ['name' => 'Manage Attendance', 'slug' => 'manage-attendance', 'module' => 'Attendance', 'description' => 'Can manage attendance'],
            
            // Payroll
            ['name' => 'View Payroll', 'slug' => 'view-payroll', 'module' => 'Payroll', 'description' => 'Can view payroll'],
            ['name' => 'Generate Payroll', 'slug' => 'generate-payroll', 'module' => 'Payroll', 'description' => 'Can generate payroll'],
            ['name' => 'Process Payroll', 'slug' => 'process-payroll', 'module' => 'Payroll', 'description' => 'Can process payroll'],
            
            // Reports
            ['name' => 'View Reports', 'slug' => 'view-reports', 'module' => 'Reports', 'description' => 'Can view reports'],
            ['name' => 'Generate Reports', 'slug' => 'generate-reports', 'module' => 'Reports', 'description' => 'Can generate reports'],
            
            // Role & Permission Management
            ['name' => 'Manage Roles', 'slug' => 'manage-roles', 'module' => 'Role Management', 'description' => 'Can manage roles'],
            ['name' => 'Manage Permissions', 'slug' => 'manage-permissions', 'module' => 'Permission Management', 'description' => 'Can manage permissions'],
        ];

        foreach ($permissions as $permission) {
            Permission::updateOrCreate(
                ['slug' => $permission['slug']],
                array_merge($permission, ['is_active' => true])
            );
        }
    }
}

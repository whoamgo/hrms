<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\MenuItem;
use App\Models\Role;

class MenuItemSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Admin Menu Items
        $adminRole = Role::where('slug', 'admin')->first();
        
        $adminMenus = [
            ['title' => 'Dashboard', 'icon' => 'mdi mdi-view-dashboard', 'route' => 'admin.dashboard', 'order' => 1, 'type' => 'admin'],
            ['title' => 'User Management', 'icon' => 'mdi mdi-account-supervisor-circle', 'route' => 'admin.users.index', 'order' => 2, 'type' => 'admin'],
            ['title' => 'Employee Master', 'icon' => 'fas fa-user', 'route' => 'admin.employees.index', 'order' => 3, 'type' => 'admin'],
            ['title' => 'Service Records', 'icon' => 'mdi mdi-file-settings', 'route' => 'admin.service-records.index', 'order' => 4, 'type' => 'admin'],
            ['title' => 'Leave Management', 'icon' => 'mdi mdi-file-document-edit', 'route' => 'admin.leaves.index', 'order' => 5, 'type' => 'admin'],
            ['title' => 'Attendance', 'icon' => 'mdi mdi-check-underline-circle', 'route' => 'admin.attendance.index', 'order' => 6, 'type' => 'admin'],
            ['title' => 'Payroll / Honorarium', 'icon' => 'mdi mdi-comment-processing', 'route' => 'admin.payroll.index', 'order' => 7, 'type' => 'admin'],
            ['title' => 'TA/DA Claim', 'icon' => 'mdi mdi-comment-text', 'route' => 'admin.tada.index', 'order' => 8, 'type' => 'admin'],
            ['title' => 'Reports', 'icon' => 'mdi mdi-file-document-edit', 'route' => 'admin.reports.index', 'order' => 9, 'type' => 'admin'],
            ['title' => 'Role Management', 'icon' => 'mdi mdi-account-key', 'route' => 'admin.roles.index', 'order' => 10, 'type' => 'admin'],
        ];

        foreach ($adminMenus as $menu) {
            $menuItem = MenuItem::updateOrCreate(
                ['route' => $menu['route']],
                array_merge($menu, ['is_active' => true, 'parent_id' => null])
            );
            
            if ($adminRole) {
                $menuItem->roles()->syncWithoutDetaching([$adminRole->id]);
            }
        }

        // HR Menu Items
        $hrRole = Role::where('slug', 'hr')->first();
        
        $hrMenus = [
            ['title' => 'Dashboard', 'icon' => 'mdi mdi-view-dashboard', 'route' => 'hr.dashboard', 'order' => 1, 'type' => 'hr'],
            ['title' => 'Employee Master', 'icon' => 'fas fa-user', 'route' => 'hr.employees.index', 'order' => 2, 'type' => 'hr'],
            ['title' => 'Attendance Management', 'icon' => 'mdi mdi-check-underline-circle', 'route' => 'hr.attendance.index', 'order' => 3, 'type' => 'hr'],
            ['title' => 'Leave Management', 'icon' => 'mdi mdi-file-document-edit', 'route' => 'hr.leaves.index', 'order' => 4, 'type' => 'hr'],
            ['title' => 'Contract Renewal Management', 'icon' => 'mdi mdi-file-settings', 'route' => 'hr.contracts.index', 'order' => 5, 'type' => 'hr'],
            ['title' => 'Payroll Coordination', 'icon' => 'mdi mdi-file-settings', 'route' => 'hr.payroll.index', 'order' => 6, 'type' => 'hr'],
            ['title' => 'Reports', 'icon' => 'mdi mdi-file-document-edit', 'route' => 'hr.reports.index', 'order' => 7, 'type' => 'hr'],
        ];

        foreach ($hrMenus as $menu) {
            $menuItem = MenuItem::updateOrCreate(
                ['route' => $menu['route']],
                array_merge($menu, ['is_active' => true, 'parent_id' => null])
            );
            
            if ($hrRole) {
                $menuItem->roles()->syncWithoutDetaching([$hrRole->id]);
            }
        }

        // Accounts Menu Items
        $accountsRole = Role::where('slug', 'accounts')->first();
        
        $accountsMenus = [
            ['title' => 'Dashboard', 'icon' => 'mdi mdi-view-dashboard', 'route' => 'accounts.dashboard', 'order' => 1, 'type' => 'accounts'],
            ['title' => 'Monthly Payroll Processing', 'icon' => 'mdi mdi-file-document-edit', 'route' => 'accounts.payroll.index', 'order' => 2, 'type' => 'accounts'],
            ['title' => 'Payment Disbursement', 'icon' => 'mdi mdi-file-settings', 'route' => 'accounts.payment.index', 'order' => 3, 'type' => 'accounts'],
            ['title' => 'TA/DA Ledger', 'icon' => 'mdi mdi-comment-text', 'route' => 'accounts.tada.index', 'order' => 4, 'type' => 'accounts'],
            ['title' => 'Reports', 'icon' => 'mdi mdi-file-document-edit', 'route' => 'accounts.reports.index', 'order' => 5, 'type' => 'accounts'],
        ];

        foreach ($accountsMenus as $menu) {
            $menuItem = MenuItem::updateOrCreate(
                ['route' => $menu['route']],
                array_merge($menu, ['is_active' => true, 'parent_id' => null])
            );
            
            if ($accountsRole) {
                $menuItem->roles()->syncWithoutDetaching([$accountsRole->id]);
            }
        }

        // Employee Menu Items
        $employeeRole = Role::where('slug', 'employee')->first();
        
        $employeeMenus = [
            ['title' => 'Dashboard', 'icon' => 'mdi mdi-view-dashboard', 'route' => 'employee.dashboard', 'order' => 1, 'type' => 'employee'],
            ['title' => 'Leave Apply', 'icon' => 'fas fa-user', 'route' => 'employee.leaves.create', 'order' => 2, 'type' => 'employee'],
            ['title' => 'Attendance', 'icon' => 'mdi mdi-check-underline-circle', 'route' => 'employee.attendance.index', 'order' => 3, 'type' => 'employee'],
            ['title' => 'TA/DA Claim', 'icon' => 'mdi mdi-comment-text', 'route' => 'employee.tada.index', 'order' => 4, 'type' => 'employee'],
            ['title' => 'View Payslips', 'icon' => 'mdi mdi-file-document-edit', 'route' => 'employee.payslips.index', 'order' => 5, 'type' => 'employee'],
        ];

        foreach ($employeeMenus as $menu) {
            $menuItem = MenuItem::updateOrCreate(
                ['route' => $menu['route']],
                array_merge($menu, ['is_active' => true, 'parent_id' => null])
            );
            
            if ($employeeRole) {
                $menuItem->roles()->syncWithoutDetaching([$employeeRole->id]);
            }
        }
    }
}

<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\User;
use App\Models\Role;
use Illuminate\Support\Facades\Hash;

class UserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $adminRole = Role::where('slug', 'admin')->first();
        $hrRole = Role::where('slug', 'hr')->first();
        $accountsRole = Role::where('slug', 'accounts')->first();
        $employeeRole = Role::where('slug', 'employee')->first();

        // Admin User
        if ($adminRole) {
            User::updateOrCreate(
                ['email' => 'admin@hrms.com'],
                [
                    'name' => 'Admin User',
                    'username' => 'admin',
                    'email' => 'admin@hrms.com',
                    'password' => Hash::make('password'),
                    'role_id' => $adminRole->id,
                    'status' => 'active',
                ]
            );
        }

        // HR User
        if ($hrRole) {
            User::updateOrCreate(
                ['email' => 'hr@hrms.com'],
                [
                    'name' => 'HR Admin',
                    'username' => 'hr',
                    'email' => 'hr@hrms.com',
                    'password' => Hash::make('password'),
                    'role_id' => $hrRole->id,
                    'status' => 'active',
                ]
            );
        }

        // Accounts User
        if ($accountsRole) {
            User::updateOrCreate(
                ['email' => 'accounts@hrms.com'],
                [
                    'name' => 'Accounts Officer',
                    'username' => 'accounts',
                    'email' => 'accounts@hrms.com',
                    'password' => Hash::make('password'),
                    'role_id' => $accountsRole->id,
                    'status' => 'active',
                ]
            );
        }

        // Employee User
        if ($employeeRole) {
            User::updateOrCreate(
                ['email' => 'employee@hrms.com'],
                [
                    'name' => 'Employee User',
                    'username' => 'employee',
                    'email' => 'employee@hrms.com',
                    'password' => Hash::make('password'),
                    'role_id' => $employeeRole->id,
                    'status' => 'active',
                ]
            );
        }
    }
}

<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Role;

class RoleSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $roles = [
            [
                'name' => 'Admin',
                'slug' => 'admin',
                'description' => 'System Administrator with full access',
                'is_active' => true,
            ],
            [
                'name' => 'HR Admin',
                'slug' => 'hr',
                'description' => 'Human Resources Administrator',
                'is_active' => true,
            ],
            [
                'name' => 'Accounts Officer',
                'slug' => 'accounts',
                'description' => 'Accounts and Finance Officer',
                'is_active' => true,
            ],
            [
                'name' => 'Employee',
                'slug' => 'employee',
                'description' => 'Regular Employee',
                'is_active' => true,
            ],
        ];

        foreach ($roles as $role) {
            Role::updateOrCreate(
                ['slug' => $role['slug']],
                $role
            );
        }
    }
}

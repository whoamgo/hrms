<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Department;

class DepartmentSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $departments = [
            [
                'name' => 'Human Resources',
                'description' => 'Human Resources Department',
                'is_active' => true,
            ],
            [
                'name' => 'Information Technology',
                'description' => 'IT Department',
                'is_active' => true,
            ],
            [
                'name' => 'Finance',
                'description' => 'Finance and Accounts Department',
                'is_active' => true,
            ],
            [
                'name' => 'Administration',
                'description' => 'Administration Department',
                'is_active' => true,
            ],
            [
                'name' => 'Operations',
                'description' => 'Operations Department',
                'is_active' => true,
            ],
            [
                'name' => 'Marketing',
                'description' => 'Marketing and Sales Department',
                'is_active' => true,
            ],
            [
                'name' => 'Research and Development',
                'description' => 'R&D Department',
                'is_active' => true,
            ],
            [
                'name' => 'Legal',
                'description' => 'Legal Department',
                'is_active' => true,
            ],
        ];

        foreach ($departments as $department) {
            Department::updateOrCreate(
                ['name' => $department['name']],
                $department
            );
        }
    }
}

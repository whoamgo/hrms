<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Designation;

class DesignationSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $designations = [
            [
                'name' => 'Chief Executive Officer',
                'description' => 'CEO',
                'is_active' => true,
            ],
            [
                'name' => 'Chief Technology Officer',
                'description' => 'CTO',
                'is_active' => true,
            ],
            [
                'name' => 'Chief Financial Officer',
                'description' => 'CFO',
                'is_active' => true,
            ],
            [
                'name' => 'Director',
                'description' => 'Director',
                'is_active' => true,
            ],
            [
                'name' => 'Manager',
                'description' => 'Manager',
                'is_active' => true,
            ],
            [
                'name' => 'Senior Manager',
                'description' => 'Senior Manager',
                'is_active' => true,
            ],
            [
                'name' => 'Assistant Manager',
                'description' => 'Assistant Manager',
                'is_active' => true,
            ],
            [
                'name' => 'Team Lead',
                'description' => 'Team Lead',
                'is_active' => true,
            ],
            [
                'name' => 'Senior Developer',
                'description' => 'Senior Developer',
                'is_active' => true,
            ],
            [
                'name' => 'Developer',
                'description' => 'Developer',
                'is_active' => true,
            ],
            [
                'name' => 'Junior Developer',
                'description' => 'Junior Developer',
                'is_active' => true,
            ],
            [
                'name' => 'HR Executive',
                'description' => 'HR Executive',
                'is_active' => true,
            ],
            [
                'name' => 'HR Manager',
                'description' => 'HR Manager',
                'is_active' => true,
            ],
            [
                'name' => 'Accountant',
                'description' => 'Accountant',
                'is_active' => true,
            ],
            [
                'name' => 'Senior Accountant',
                'description' => 'Senior Accountant',
                'is_active' => true,
            ],
            [
                'name' => 'Administrative Assistant',
                'description' => 'Administrative Assistant',
                'is_active' => true,
            ],
            [
                'name' => 'Executive Assistant',
                'description' => 'Executive Assistant',
                'is_active' => true,
            ],
            [
                'name' => 'Marketing Executive',
                'description' => 'Marketing Executive',
                'is_active' => true,
            ],
            [
                'name' => 'Sales Executive',
                'description' => 'Sales Executive',
                'is_active' => true,
            ],
            [
                'name' => 'Intern',
                'description' => 'Intern',
                'is_active' => true,
            ],
        ];

        foreach ($designations as $designation) {
            Designation::updateOrCreate(
                ['name' => $designation['name']],
                $designation
            );
        }
    }
}

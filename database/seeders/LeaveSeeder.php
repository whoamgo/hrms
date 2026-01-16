<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Leave;
use App\Models\Employee;
use Carbon\Carbon;

class LeaveSeeder extends Seeder
{
    public function run(): void
    {
        $employees = Employee::where('status', 'active')->get();

        if ($employees->isEmpty()) {
            $this->command->warn('No active employees found. Please run EmployeeSeeder first.');
            return;
        }

        $leaves = [
            [
                'employee_id' => $employees->random()->id,
                'leave_type' => 'CL',
                'from_date' => Carbon::parse('2025-01-15'),
                'to_date' => Carbon::parse('2025-01-17'),
                'total_days' => 3,
                'reason' => 'Personal work',
                'status' => 'approved',
            ],
            [
                'employee_id' => $employees->random()->id,
                'leave_type' => 'SL',
                'from_date' => Carbon::parse('2025-01-20'),
                'to_date' => Carbon::parse('2025-01-21'),
                'total_days' => 2,
                'reason' => 'Fever',
                'status' => 'pending',
            ],
            [
                'employee_id' => $employees->random()->id,
                'leave_type' => 'CL',
                'from_date' => Carbon::parse('2025-02-01'),
                'to_date' => Carbon::parse('2025-02-03'),
                'total_days' => 3,
                'reason' => 'Family function',
                'status' => 'approved',
            ],
            [
                'employee_id' => $employees->random()->id,
                'leave_type' => 'SPL',
                'from_date' => Carbon::parse('2025-02-10'),
                'to_date' => Carbon::parse('2025-02-12'),
                'total_days' => 3,
                'reason' => 'Special occasion',
                'status' => 'pending',
            ],
            [
                'employee_id' => $employees->random()->id,
                'leave_type' => 'CL',
                'from_date' => Carbon::parse('2025-02-15'),
                'to_date' => Carbon::parse('2025-02-15'),
                'total_days' => 1,
                'reason' => 'Personal',
                'status' => 'rejected',
                'rejection_reason' => 'Workload is high',
            ],
            [
                'employee_id' => $employees->random()->id,
                'leave_type' => 'SL',
                'from_date' => Carbon::parse('2025-02-20'),
                'to_date' => Carbon::parse('2025-02-22'),
                'total_days' => 3,
                'reason' => 'Medical checkup',
                'status' => 'approved',
            ],
            [
                'employee_id' => $employees->random()->id,
                'leave_type' => 'CL',
                'from_date' => Carbon::parse('2025-03-01'),
                'to_date' => Carbon::parse('2025-03-05'),
                'total_days' => 5,
                'reason' => 'Vacation',
                'status' => 'pending',
            ],
            [
                'employee_id' => $employees->random()->id,
                'leave_type' => 'SL',
                'from_date' => Carbon::parse('2025-03-10'),
                'to_date' => Carbon::parse('2025-03-10'),
                'total_days' => 1,
                'reason' => 'Sick',
                'status' => 'approved',
            ],
            [
                'employee_id' => $employees->random()->id,
                'leave_type' => 'CL',
                'from_date' => Carbon::parse('2025-03-15'),
                'to_date' => Carbon::parse('2025-03-16'),
                'total_days' => 2,
                'reason' => 'Personal work',
                'status' => 'pending',
            ],
            [
                'employee_id' => $employees->random()->id,
                'leave_type' => 'SPL',
                'from_date' => Carbon::parse('2025-03-20'),
                'to_date' => Carbon::parse('2025-03-24'),
                'total_days' => 5,
                'reason' => 'Special leave',
                'status' => 'approved',
            ],
        ];

        foreach ($leaves as $leaveData) {
            Leave::create($leaveData);
        }

        $this->command->info('10 leave records created successfully!');
    }
}

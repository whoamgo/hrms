<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\ServiceRecord;
use App\Models\Employee;
use Carbon\Carbon;

class ServiceRecordSeeder extends Seeder
{
    public function run(): void
    {
        $employees = Employee::all();

        if ($employees->isEmpty()) {
            $this->command->warn('No employees found. Please run EmployeeSeeder first.');
            return;
        }

        $serviceRecords = [
            [
                'employee_id' => $employees->random()->id,
                'from_date' => Carbon::parse('2020-01-15'),
                'to_date' => Carbon::parse('2022-12-31'),
                'designation' => 'Junior Assistant',
                'department' => 'Accounts',
                'remarks' => 'Initial Appointment',
                'status' => 'inactive',
            ],
            [
                'employee_id' => $employees->random()->id,
                'from_date' => Carbon::parse('2023-01-01'),
                'to_date' => null,
                'designation' => 'Senior Assistant',
                'department' => 'Accounts',
                'remarks' => 'Promotion',
                'status' => 'active',
            ],
            [
                'employee_id' => $employees->random()->id,
                'from_date' => Carbon::parse('2019-06-01'),
                'to_date' => Carbon::parse('2021-05-31'),
                'designation' => 'HR Executive',
                'department' => 'HR',
                'remarks' => 'Initial Appointment',
                'status' => 'inactive',
            ],
            [
                'employee_id' => $employees->random()->id,
                'from_date' => Carbon::parse('2021-06-01'),
                'to_date' => null,
                'designation' => 'HR Manager',
                'department' => 'HR',
                'remarks' => 'Promotion',
                'status' => 'active',
            ],
            [
                'employee_id' => $employees->random()->id,
                'from_date' => Carbon::parse('2020-09-01'),
                'to_date' => Carbon::parse('2022-08-31'),
                'designation' => 'Software Developer',
                'department' => 'IT',
                'remarks' => 'Initial Appointment',
                'status' => 'inactive',
            ],
            [
                'employee_id' => $employees->random()->id,
                'from_date' => Carbon::parse('2022-09-01'),
                'to_date' => null,
                'designation' => 'Senior Software Developer',
                'department' => 'IT',
                'remarks' => 'Promotion',
                'status' => 'active',
            ],
            [
                'employee_id' => $employees->random()->id,
                'from_date' => Carbon::parse('2018-10-15'),
                'to_date' => Carbon::parse('2020-10-14'),
                'designation' => 'Accountant',
                'department' => 'Finance',
                'remarks' => 'Initial Appointment',
                'status' => 'inactive',
            ],
            [
                'employee_id' => $employees->random()->id,
                'from_date' => Carbon::parse('2020-10-15'),
                'to_date' => null,
                'designation' => 'Finance Manager',
                'department' => 'Finance',
                'remarks' => 'Promotion',
                'status' => 'active',
            ],
            [
                'employee_id' => $employees->random()->id,
                'from_date' => Carbon::parse('2021-07-10'),
                'to_date' => null,
                'designation' => 'Marketing Executive',
                'department' => 'Marketing',
                'remarks' => 'Initial Appointment',
                'status' => 'active',
            ],
            [
                'employee_id' => $employees->random()->id,
                'from_date' => Carbon::parse('2017-08-20'),
                'to_date' => Carbon::parse('2019-08-19'),
                'designation' => 'Operations Executive',
                'department' => 'Operations',
                'remarks' => 'Initial Appointment',
                'status' => 'inactive',
            ],
            [
                'employee_id' => $employees->random()->id,
                'from_date' => Carbon::parse('2019-08-20'),
                'to_date' => null,
                'designation' => 'Operations Manager',
                'department' => 'Operations',
                'remarks' => 'Promotion',
                'status' => 'active',
            ],
            [
                'employee_id' => $employees->random()->id,
                'from_date' => Carbon::parse('2020-06-01'),
                'to_date' => null,
                'designation' => 'Sales Manager',
                'department' => 'Sales',
                'remarks' => 'Initial Appointment',
                'status' => 'active',
            ],
            [
                'employee_id' => $employees->random()->id,
                'from_date' => Carbon::parse('2021-03-15'),
                'to_date' => null,
                'designation' => 'QA Engineer',
                'department' => 'IT',
                'remarks' => 'Initial Appointment',
                'status' => 'active',
            ],
            [
                'employee_id' => $employees->random()->id,
                'from_date' => Carbon::parse('2020-04-10'),
                'to_date' => null,
                'designation' => 'Backend Developer',
                'department' => 'IT',
                'remarks' => 'Initial Appointment',
                'status' => 'active',
            ],
            [
                'employee_id' => $employees->random()->id,
                'from_date' => Carbon::parse('2022-01-10'),
                'to_date' => null,
                'designation' => 'Sales Executive',
                'department' => 'Sales',
                'remarks' => 'Initial Appointment',
                'status' => 'active',
            ],
        ];

        foreach ($serviceRecords as $recordData) {
            ServiceRecord::create($recordData);
        }

        $this->command->info('15 service records created successfully!');
    }
}

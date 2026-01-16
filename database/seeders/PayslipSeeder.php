<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Payslip;
use App\Models\Employee;
use Carbon\Carbon;

class PayslipSeeder extends Seeder
{
    public function run(): void
    {
        $employees = Employee::where('status', 'active')->take(5)->get();

        if ($employees->isEmpty()) {
            $this->command->warn('No active employees found. Please run EmployeeSeeder first.');
            return;
        }

        $currentMonth = Carbon::now()->format('F');
        $currentYear = Carbon::now()->format('Y');
        $lastMonth = Carbon::now()->subMonth()->format('F');
        $lastYear = Carbon::now()->subMonth()->format('Y');

        foreach ($employees as $employee) {
            // Current month payslip
            Payslip::create([
                'employee_id' => $employee->id,
                'month' => $currentMonth,
                'year' => $currentYear,
                'basic_salary' => 18250.00,
                'hra' => 0.00,
                'conveyance_allowance' => 0.00,
                'medical_allowance' => 0.00,
                'special_allowance' => 0.00,
                'esi' => 0.00,
                'pf' => 0.00,
                'tds' => 0.00,
                'deduction_10_percent' => 0.00,
                'mobile_deduction' => 0.00,
                'comp_off' => 0.00,
                'total_earnings' => 18250.00,
                'total_deductions' => 0.00,
                'salary_payable' => 18250.00,
                'days_payable' => 30.00,
            ]);

            // Last month payslip
            Payslip::create([
                'employee_id' => $employee->id,
                'month' => $lastMonth,
                'year' => $lastYear,
                'basic_salary' => 18250.00,
                'hra' => 0.00,
                'conveyance_allowance' => 0.00,
                'medical_allowance' => 0.00,
                'special_allowance' => 0.00,
                'esi' => 0.00,
                'pf' => 0.00,
                'tds' => 0.00,
                'deduction_10_percent' => 0.00,
                'mobile_deduction' => 0.00,
                'comp_off' => 0.00,
                'total_earnings' => 18250.00,
                'total_deductions' => 0.00,
                'salary_payable' => 18250.00,
                'days_payable' => 30.00,
            ]);
        }

        $this->command->info('Payslips created successfully for ' . $employees->count() . ' employees!');
    }
}

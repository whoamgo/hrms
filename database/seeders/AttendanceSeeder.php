<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Attendance;
use App\Models\Employee;
use Carbon\Carbon;

class AttendanceSeeder extends Seeder
{
    public function run(): void
    {
        $employees = Employee::where('status', 'active')->take(5)->get();

        if ($employees->isEmpty()) {
            $this->command->warn('No active employees found. Please run EmployeeSeeder first.');
            return;
        }

        $startDate = Carbon::now()->startOfMonth();
        $endDate = Carbon::now()->endOfMonth();

        foreach ($employees as $employee) {
            $currentDate = $startDate->copy();
            
            while ($currentDate->lte($endDate)) {
                $dayOfWeek = $currentDate->dayOfWeek;
                
                // Skip weekends (Saturday = 6, Sunday = 0)
                if ($dayOfWeek == 0 || $dayOfWeek == 6) {
                    Attendance::create([
                        'employee_id' => $employee->id,
                        'date' => $currentDate->copy(),
                        'status' => 'weekend',
                        'check_in' => null,
                        'check_out' => null,
                        'working_hours' => null,
                    ]);
                } else {
                    // Randomly assign present/absent for weekdays
                    $isPresent = rand(0, 10) > 2; // 80% chance of being present
                    
                    if ($isPresent) {
                        $checkIn = Carbon::createFromTime(9, rand(0, 30), 0);
                        $checkOut = Carbon::createFromTime(18, rand(0, 30), 0);
                        $diff = $checkIn->diff($checkOut);
                        $workingHours = $diff->h . ' Hrs:' . str_pad($diff->i, 2, '0', STR_PAD_LEFT) . ' Min';
                        
                        Attendance::create([
                            'employee_id' => $employee->id,
                            'date' => $currentDate->copy(),
                            'status' => 'present',
                            'check_in' => $checkIn->format('H:i:s'),
                            'check_out' => $checkOut->format('H:i:s'),
                            'working_hours' => $workingHours,
                        ]);
                    } else {
                        Attendance::create([
                            'employee_id' => $employee->id,
                            'date' => $currentDate->copy(),
                            'status' => 'absent',
                            'check_in' => null,
                            'check_out' => null,
                            'working_hours' => null,
                        ]);
                    }
                }
                
                $currentDate->addDay();
            }
        }

        $this->command->info('Attendance records created successfully for ' . $employees->count() . ' employees!');
    }
}

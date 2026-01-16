<?php

namespace App\Http\Controllers\Employee;

use App\Http\Controllers\Controller;
use App\Models\Attendance;
use App\Models\Leave;
use App\Models\Employee;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Carbon\Carbon;

class AttendanceController extends Controller
{
    /**
     * Display employee's attendance.
     */
    public function index(Request $request)
    {
        try {
            $user = Auth::user();
            $employee = $user->employee;
            
            if (!$employee) {
                return redirect()->route('employee.dashboard')->with('error', 'Employee record not found.');
            }

            // Get current month and year if not provided
            $month = $request->get('month', date('n'));
            $year = $request->get('year', date('Y'));

            // Calculate leave balance (CL - used leaves)
            $usedLeaves = Leave::where('employee_id', $employee->id)
                ->where('leave_type', 'CL')
                ->where('status', 'approved')
                ->sum('total_days');
            
            $clBalance = max(0, 12 - $usedLeaves); // Assuming 12 CL per year

            return view('employee.attendances.index', compact('employee', 'month', 'year', 'clBalance'));
        } catch (\Exception $e) {
            return redirect()->route('employee.dashboard')->with('error', 'Error loading attendance: ' . $e->getMessage());
        }
    }

    /**
     * Get employee's attendance data
     */
    public function getMyAttendance(Request $request)
    {
        try {
            $user = Auth::user();
            $employee = $user->employee;
            
            if (!$employee) {
                return response()->json(['error' => 'Employee record not found.'], 404);
            }

            $month = $request->get('month', date('n'));
            $year = $request->get('year', date('Y'));

            $startDate = Carbon::create($year, $month, 1)->startOfMonth();
            $endDate = $startDate->copy()->endOfMonth();

            $attendances = Attendance::where('employee_id', $employee->id)
                ->whereBetween('date', [$startDate->format('Y-m-d'), $endDate->format('Y-m-d')])
                ->orderBy('date', 'asc')
                ->get();

            // Create a map of attendances by date for faster lookup
            $attendanceMap = [];
            foreach ($attendances as $att) {
                $dateKey = $att->date->format('Y-m-d');
                $attendanceMap[$dateKey] = $att;
            }

            $data = [];
            $currentDate = $startDate->copy();
            
            while ($currentDate->lte($endDate)) {
                // Use date map for faster lookup
                $dateKey = $currentDate->format('Y-m-d');
                $attendance = $attendanceMap[$dateKey] ?? null;
                
                $day = $currentDate->format('l');
                $rowClass = '';
                
                if ($attendance) {
                    if ($attendance->status == 'weekend') {
                        $day .= ' (Weekend-Holiday)';
                        $rowClass = 'btn-secondary';
                    } elseif ($attendance->status == 'holiday') {
                        $day .= ' (Festival)';
                        $rowClass = 'btn-success';
                    } elseif ($attendance->status == 'absent') {
                        // Check if there's an approved leave
                        $leave = Leave::where('employee_id', $employee->id)
                            ->where('from_date', '<=', $currentDate)
                            ->where('to_date', '>=', $currentDate)
                            ->where('status', 'approved')
                            ->first();
                        
                        if ($leave) {
                            $day .= ' (Leave)';
                            $rowClass = 'btn-purple'; // Leave
                        } else {
                            $rowClass = 'btn-info'; // Absent
                        }
                    } elseif ($attendance->status == 'present') {
                        $rowClass = ''; // Present - no special styling
                    }
                    
                    // Format check-in time
                    if ($attendance->check_in) {
                        if (is_string($attendance->check_in)) {
                            // If it's already a time string like "09:30:00", extract just the time part
                            $checkIn = substr($attendance->check_in, 0, 8);
                        } else {
                            $checkIn = Carbon::parse($attendance->check_in)->format('H:i:s');
                        }
                    } else {
                        $checkIn = '-';
                    }
                    
                    // Format check-out time
                    if ($attendance->check_out) {
                        if (is_string($attendance->check_out)) {
                            $checkOut = substr($attendance->check_out, 0, 8);
                        } else {
                            $checkOut = Carbon::parse($attendance->check_out)->format('H:i:s');
                        }
                    } else {
                        $checkOut = '-';
                    }
                    
                    $workingHours = $attendance->calculateWorkingHours();
                } else {
                    // No attendance record
                    $dayOfWeek = $currentDate->dayOfWeek;
                    if ($dayOfWeek == 0 || $dayOfWeek == 6) {
                        $day .= ' (Weekend-Holiday)';
                        $rowClass = 'btn-secondary';
                    } else {
                        $rowClass = '';
                    }
                    $checkIn = '-';
                    $checkOut = '-';
                    $workingHours = '-';
                }

                $data[] = [
                    'date' => $currentDate->format('d/m/Y'),
                    'day' => $day,
                    'check_in' => $checkIn,
                    'check_out' => $checkOut,
                    'working_hours' => $workingHours,
                    'row_class' => $rowClass,
                ];

                $currentDate->addDay();
            }

            return response()->json([
                'success' => true,
                'data' => $data
            ]);
        } catch (\Exception $e) {
            return response()->json(['error' => 'Error loading attendance: ' . $e->getMessage()], 500);
        }
    }
}

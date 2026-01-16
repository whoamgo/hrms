<?php

namespace App\Http\Controllers\Employee;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\Leave;
use App\Models\Payslip;
use App\Models\TadaClaim;
use Carbon\Carbon;

class DashboardController extends Controller
{
    /**
     * Show the employee dashboard.
     */
    public function index()
    {
        try {
            $user = Auth::user();
            $employee = $user->employee;
            
            if (!$employee) {
                return view('employee.dashboard', [
                    'employee' => null,
                    'stats' => [
                        'leave_balance' => 0,
                        'latest_payslip' => 0,
                        'pending_tada_claims' => 0,
                    ]
                ])->with('error', 'Employee record not found. Please contact HR to create your employee profile.');
            }

            // Calculate leave balance (assuming default leave balance, you can adjust this)
            $leaveBalance = 0; // You can add logic to calculate from leave records
            $approvedLeaves = Leave::where('employee_id', $employee->id)
                ->where('status', 'approved')
                ->sum('total_days');
            // Assuming default leave balance is 12 days per year
            $leaveBalance = max(0, 12 - $approvedLeaves);

            // Get latest payslip count
            $latestPayslip = Payslip::where('employee_id', $employee->id)
                ->orderBy('created_at', 'desc')
                ->count();

            // Get pending TA/DA claims for current month
            $pendingTadaClaims = TadaClaim::where('employee_id', $employee->id)
                ->where('status', 'pending')
                ->whereMonth('travel_date', Carbon::now()->month)
                ->whereYear('travel_date', Carbon::now()->year)
                ->count();

            $stats = [
                'leave_balance' => $leaveBalance,
                'latest_payslip' => $latestPayslip,
                'pending_tada_claims' => $pendingTadaClaims,
            ];

            return view('employee.dashboard', compact('employee', 'stats'));
        } catch (\Exception $e) {
            return view('employee.dashboard', [
                'employee' => null,
                'stats' => [
                    'leave_balance' => 0,
                    'latest_payslip' => 0,
                    'pending_tada_claims' => 0,
                ]
            ])->with('error', 'Error loading dashboard: ' . $e->getMessage());
        }
    }
}


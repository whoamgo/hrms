<?php

namespace App\Http\Controllers\HR;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;
use App\Models\Leave;
use App\Models\Attendance;
use App\Models\Employee;
use App\Models\Payslip;
use Carbon\Carbon;

class DashboardController extends Controller
{
    public function index()
    {
        try {
            $stats = Cache::remember('hr_dashboard_stats', 300, function () {
                return [
                    'pending_leaves' => Leave::where('status', 'pending')->count(),
                    'today_attendance' => Attendance::whereDate('date', today())->count(),
                    'expiring_contracts' => Employee::where('employee_type', 'Contract')
                        ->where('status', 'active')
                        ->whereNotNull('contract_end_date')
                        ->whereBetween('contract_end_date', [now(), now()->addDays(30)])
                        ->count(),
                    'expired_contracts' => Employee::where('employee_type', 'Contract')
                        ->where('status', 'active')
                        ->whereNotNull('contract_end_date')
                        ->where('contract_end_date', '<', now())
                        ->count(),
                    'payroll_pending' => Payslip::where('year', date('Y'))
                        ->where('month', date('F'))
                        ->count(),
                ];
            });

            return view('hr.dashboard', compact('stats'));
        } catch (\Exception $e) {
            return redirect()->back()->with('error', 'Error loading dashboard: ' . $e->getMessage());
        }
    }
}


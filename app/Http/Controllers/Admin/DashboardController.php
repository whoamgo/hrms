<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;
use App\Models\User;
use App\Models\MenuItem;

class DashboardController extends Controller
{
    /**
     * Show the admin dashboard.
     */
    public function index()
    {
        try {
            // Get dashboard statistics with cache
            $stats = Cache::remember('admin_dashboard_stats', 300, function () {
                $employeeRoleId = \App\Models\Role::where('slug', 'employee')->value('id');
                
                return [
                    'total_employees' => \App\Models\Employee::where('status', 'active')->count(),
                    'active_contracts' => \App\Models\Employee::where('employee_type', 'Contract')
                        ->where('status', 'active')
                        ->whereNotNull('contract_end_date')
                        ->where('contract_end_date', '>=', now())
                        ->count(),
                    'expiring_contracts' => \App\Models\Employee::where('employee_type', 'Contract')
                        ->where('status', 'active')
                        ->whereNotNull('contract_end_date')
                        ->whereBetween('contract_end_date', [now(), now()->addDays(30)])
                        ->count(),
                    'pending_leaves' => \App\Models\Leave::where('status', 'pending')->count(),
                    'payroll_generated' => \App\Models\Payslip::where('year', date('Y'))
                        ->where('month', date('F'))
                        ->count(),
                    'total_users' => User::where('status', 'active')->count(),
                    'pending_tada_claims' => \App\Models\TadaClaim::where('status', 'pending')->count(),
                ];
            });

            return view('admin.dashboard', compact('stats'));
        } catch (\Exception $e) {
            return redirect()->back()->with('error', 'Error loading dashboard: ' . $e->getMessage());
        }
    }
}

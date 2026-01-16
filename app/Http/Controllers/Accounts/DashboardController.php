<?php

namespace App\Http\Controllers\Accounts;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;
use App\Models\Payslip;
use App\Models\PaymentDisbursement;
use App\Models\TadaClaim;

class DashboardController extends Controller
{
    public function index()
    {
        try {
            $stats = Cache::remember('accounts_dashboard_stats', 300, function () {
                $currentMonth = date('F');
                $currentYear = date('Y');
                
                return [
                    'pending_payroll' => Payslip::where('year', $currentYear)
                        ->where('month', $currentMonth)
                        ->count(),
                    'payroll_generated' => Payslip::where('year', $currentYear)
                        ->where('month', $currentMonth)
                        ->count(),
                    'pending_honorarium' => PaymentDisbursement::where('disbursement_status', 'Pending')
                        ->count(),
                    'pending_tada_claims' => TadaClaim::where('status', 'pending')->count(),
                    'total_disbursements' => PaymentDisbursement::where('year', $currentYear)
                        ->where('month', $currentMonth)
                        ->count(),
                    'successful_disbursements' => PaymentDisbursement::where('year', $currentYear)
                        ->where('month', $currentMonth)
                        ->where('disbursement_status', 'Success')
                        ->count(),
                ];
            });

            return view('accounts.dashboard', compact('stats'));
        } catch (\Exception $e) {
            return redirect()->back()->with('error', 'Error loading dashboard: ' . $e->getMessage());
        }
    }
}


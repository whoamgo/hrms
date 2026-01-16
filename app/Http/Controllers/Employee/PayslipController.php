<?php

namespace App\Http\Controllers\Employee;

use App\Http\Controllers\Controller;
use App\Models\Payslip;
use App\Models\Employee;
use App\Models\Attendance;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Carbon\Carbon;
use Barryvdh\DomPDF\Facade\Pdf as PDF;

class PayslipController extends Controller
{
    /**
     * Display employee's payslips.
     */
    public function index(Request $request)
    {
        try {
            $user = Auth::user();
            $employee = $user->employee;
            
            if (!$employee) {
                return redirect()->route('employee.dashboard')->with('error', 'Employee record not found.');
            }

            $month = $request->get('month', date('n'));
            $year = $request->get('year', date('Y'));

            return view('employee.payslips.index', compact('employee', 'month', 'year'));
        } catch (\Exception $e) {
            return redirect()->route('employee.dashboard')->with('error', 'Error loading payslips: ' . $e->getMessage());
        }
    }

    /**
     * Get employee's payslips
     */
    public function getMyPayslips(Request $request)
    {
        try {
            $user = Auth::user();
            $employee = $user->employee;
            
            if (!$employee) {
                return response()->json(['error' => 'Employee record not found.'], 404);
            }

            $month = $request->get('month', date('n'));
            $year = $request->get('year', date('Y'));

            $monthName = Carbon::create($year, $month, 1)->format('F');

            $payslip = Payslip::where('employee_id', $employee->id)
                ->where('month', $monthName)
                ->where('year', $year)
                ->first();

            if ($payslip) {
                // Calculate present days from attendance
                $startDate = Carbon::create($year, $month, 1)->startOfMonth();
                $endDate = $startDate->copy()->endOfMonth();
                
                $presentDays = Attendance::where('employee_id', $employee->id)
                    ->whereBetween('date', [$startDate, $endDate])
                    ->where('status', 'present')
                    ->count();
                    
                return response()->json([
                    'success' => true,
                    'data' => [
                        [
                            'id' => $payslip->id,
                            'name' => $employee->full_name,
                            'present_days' => number_format($presentDays, 2),
                            'month' => $monthName,
                            'year' => $year,
                        ]
                    ]
                ]);
            }

            return response()->json([
                'success' => true,
                'data' => []
            ]);
        } catch (\Exception $e) {
            return response()->json(['error' => 'Error loading payslips: ' . $e->getMessage()], 500);
        }
    }

    /**
     * Display the specified payslip.
     */
    public function show($id)
    {
        try {
            $user = Auth::user();
            $employee = $user->employee;
            
            if (!$employee) {
                return redirect()->route('employee.payslips.index')->with('error', 'Employee record not found.');
            }

            $payslip = Payslip::where('id', $id)
                ->where('employee_id', $employee->id)
                ->firstOrFail();

            $payslip->load('employee');

            return view('employee.payslips.show', compact('payslip', 'employee'));
        } catch (\Exception $e) {
            return redirect()->route('employee.payslips.index')->with('error', 'Error loading payslip: ' . $e->getMessage());
        }
    }

    /**
     * Generate PDF for payslip
     */
    public function pdf($id)
    {
        try {
            $user = Auth::user();
            $employee = $user->employee;
            
            if (!$employee) {
                abort(404);
            }

            $payslip = Payslip::where('id', $id)
                ->where('employee_id', $employee->id)
                ->firstOrFail();

            $payslip->load('employee');

            $pdf = PDF::loadView('employee.payslips.pdf', compact('payslip', 'employee'));
            
            return $pdf->download('payslip-' . $payslip->month . '-' . $payslip->year . '.pdf');
        } catch (\Exception $e) {
            return redirect()->route('employee.payslips.index')->with('error', 'Error generating PDF: ' . $e->getMessage());
        }
    }
}

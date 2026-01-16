<?php

namespace App\Http\Controllers\HR;

use App\Http\Controllers\Controller;
use App\Models\Payslip;
use App\Models\Employee;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Validator;
use Carbon\Carbon;
use Barryvdh\DomPDF\Facade\Pdf as PDF;

class PayrollController extends Controller
{
    public function index()
    {
        try {
            \App\Helpers\ActivityLogHelper::log('viewed', null, 'HR - Payroll Coordination');
            return view('hr.payroll.index');
        } catch (\Exception $e) {
            return redirect()->route('hr.dashboard')->with('error', 'Error loading payroll: ' . $e->getMessage());
        }
    }

    public function getPayrolls(Request $request)
    {
        try {
            $query = Payslip::with('employee');

            if ($request->has('search') && $request->search['value']) {
                $search = $request->search['value'];
                $query->where(function($q) use ($search) {
                    $q->whereHas('employee', function($q2) use ($search) {
                        $q2->where('full_name', 'like', "%{$search}%")
                           ->orWhere('employee_id', 'like', "%{$search}%");
                    })
                    ->orWhere('month', 'like', "%{$search}%")
                    ->orWhere('year', 'like', "%{$search}%");
                });
            }

           if ($request->filled('month')) {
                $date = Carbon::createFromFormat('Y-m', $request->month);
                $year  = $date->year;
                $month = $date->format('F');
                $query->where('year', $year)
                      ->where('month', $month);
            }

            if ($request->filled('employee_type')) {
                $query->whereHas('employee', function($q) use ($request) {
                    $q->where('employee_type', $request->employee_type);
                });
            }

            if ($request->filled('status')) {
                // Status logic can be based on whether payslip exists or not
                if ($request->status == 'Generated') {
                    // Already have payslip
                } else {
                    // Not generated - would need to check employees without payslips
                }
            }

            $totalRecords = Payslip::count();
            $filteredRecords = $query->count();

            $orderColumn = $request->order[0]['column'] ?? 0;
            $orderDir = $request->order[0]['dir'] ?? 'desc';
            $columns = ['id', 'month', 'employee_id', 'salary_payable', 'created_at'];
            $orderBy = $columns[$orderColumn] ?? 'id';
            $query->orderBy($orderBy, $orderDir);

            $start = $request->start ?? 0;
            $length = $request->length ?? 10;
            $payrolls = $query->skip($start)->take($length)->get();

            $data = [];
            foreach ($payrolls as $payroll) {
                $basicPay = $payroll->basic_salary;
                $allowances = $payroll->hra + $payroll->conveyance_allowance + $payroll->medical_allowance + $payroll->special_allowance;
                $deductions = $payroll->esi + $payroll->pf + $payroll->tds + $payroll->deduction_10_percent + $payroll->mobile_deduction + $payroll->comp_off;
                $netPay = $payroll->salary_payable;

                $data[] = [
                    'id' => $payroll->id,
                    'month' => $payroll->month . '-' . $payroll->year,
                    'employee_name' => $payroll->employee->full_name ?? 'N/A',
                    'employee_type' => $payroll->employee->employee_type ?? 'N/A',
                    'basic_pay' => '₹' . number_format($basicPay, 2),
                    'allowances' => '₹' . number_format($allowances, 2),
                    'deductions' => '₹' . number_format($deductions, 2),
                    'net_pay' => '₹' . number_format($netPay, 2),
                    'status' => 'Generated',
                    'actions' => view('hr.payroll.partials.actions', ['payroll' => $payroll])->render(),
                ];
            }

            return response()->json([
                'draw' => intval($request->draw),
                'recordsTotal' => $totalRecords,
                'recordsFiltered' => $filteredRecords,
                'data' => $data
            ]);
        } catch (\Exception $e) {
            return response()->json(['error' => 'Error loading payroll: ' . $e->getMessage()], 500);
        }
    }

    public function show(Payslip $payslip, Request $request)
    {
        try {
            $payslip->load('employee');
            if ($request->ajax()) {
                return view('hr.payroll.partials.view-modal', compact('payslip'))->render();
            }
            return view('hr.payroll.show', compact('payslip'));
        } catch (\Exception $e) {
            if ($request->ajax()) {
                return response()->json(['error' => 'Error loading payslip: ' . $e->getMessage()], 500);
            }
            return redirect()->route('hr.payroll.index')->with('error', 'Error loading payslip: ' . $e->getMessage());
        }
    }


    public function generatePdf(Payslip $payslip)
    {
        try {
            $payslip->load('employee');
            $pdf = PDF::loadView('hr.payroll.pdf', compact('payslip'));
            return $pdf->download('payslip-' . $payslip->month . '-' . $payslip->year . '-' . $payslip->employee->employee_id . '.pdf');
        } catch (\Exception $e) {
            return redirect()->back()->with('error', 'Error generating PDF: ' . $e->getMessage());
        }
    }
}

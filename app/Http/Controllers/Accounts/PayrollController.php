<?php

namespace App\Http\Controllers\Accounts;

use App\Http\Controllers\Controller;
use App\Models\Payslip;
use App\Models\Employee;
use App\Models\Role;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Validator;
use Carbon\Carbon;
use Barryvdh\DomPDF\Facade\Pdf as PDF;
use Illuminate\Support\Facades\Crypt;
class PayrollController extends Controller
{
    public function index()
    {
        try {
            $roles = Cache::remember('all_roles', 3600, function() {
                return Role::where('is_active', true)->where('slug','employee')->get();
            });
            return view('accounts.payroll.index', compact('roles'));
        } catch (\Exception $e) {
            return redirect()->route('accounts.dashboard')->with('error', 'Error loading payroll: ' . $e->getMessage());
        }
    }

    public function getEmployeesByRole(Request $request)
    {
        try {
            $roleSlug = $request->get('role');
            $employeeType = $request->get('employee_type');
            
            $query = Employee::where('status', 'active');
            
            if ($roleSlug) {
                $role = Role::where('slug', $roleSlug)->first();
                if ($role) {
                    $query->whereHas('user', function($q) use ($role) {
                        $q->where('role_id', $role->id);
                    });
                }
            }
            
            if ($employeeType) {
                $query->where('employee_type', $employeeType);
            }
            
            $employees = $query->get();
            
            return response()->json([
                'success' => true,
                'employees' => $employees->map(function($emp) {
                    return [
                        'id' => $emp->id,
                        'name' => $emp->full_name . ' (' . $emp->employee_id . ')',
                        'employee_id' => $emp->employee_id,
                        'employee_type' => $emp->employee_type,
                    ];
                })
            ]);
        } catch (\Exception $e) {
            return response()->json(['success' => false, 'message' => $e->getMessage()], 500);
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
                $query->where('month', $request->month);
            }

            if ($request->filled('year')) {
                $query->where('year', $request->year);
            }

            if ($request->filled('role')) {
                $role = Role::where('slug', $request->role)->first();
                if ($role) {
                    $query->whereHas('employee.user', function($q) use ($role) {
                        $q->where('role_id', $role->id);
                    });
                }
            }

            if ($request->filled('employee_type')) {
                $query->whereHas('employee', function($q) use ($request) {
                    $q->where('employee_type', $request->employee_type);
                });
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
                $deductions = $payroll->esi + $payroll->pf + $payroll->tds + ($payroll->other_deductions ?? 0) + $payroll->mobile_deduction + $payroll->comp_off;
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
                    'actions' => view('accounts.payroll.partials.actions', ['payroll' => $payroll])->render(),
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

    public function store(Request $request)
    {
        try {
            $validator = Validator::make($request->all(), [
                'employee_id' => 'required|exists:employees,id',
                'month' => 'required|string',
                'year' => 'required|integer',
                'basic_salary' => 'required|numeric|min:0',
                'allowances' => 'nullable|numeric|min:0',
                'hra' => 'nullable|numeric|min:0',
                'conveyance_allowance' => 'nullable|numeric|min:0',
                'medical_allowance' => 'nullable|numeric|min:0',
                'special_allowance' => 'nullable|numeric|min:0',
                'esi' => 'nullable|numeric|min:0',
                'pf' => 'nullable|numeric|min:0',
                'tds' => 'nullable|numeric|min:0',
                'other_deductions' => 'nullable|numeric|min:0',
                'mobile_deduction' => 'nullable|numeric|min:0',
                'comp_off' => 'nullable|numeric|min:0',
                'days_payable' => 'required|numeric|min:0',
            ]);

            if ($validator->fails()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Validation failed.',
                    'errors' => $validator->errors()
                ], 422);
            }

            // Check for existing payslip
            $existingPayslip = Payslip::where('employee_id', $request->employee_id)
                                    ->where('month', $request->month)
                                    ->where('year', $request->year)
                                    ->first();

            if ($existingPayslip) {
                return response()->json([
                    'success' => false,
                    'message' => 'Payslip for this employee for the selected month and year already exists.',
                    'errors' => ['month' => ['Payslip already exists.']]
                ], 422);
            }

            // If allowances is provided as single field, use it; otherwise sum individual allowances
            $totalAllowances = $request->allowances ?? 0;
            if ($totalAllowances == 0) {
                $totalAllowances = ($request->hra ?? 0) + ($request->conveyance_allowance ?? 0) +
                                  ($request->medical_allowance ?? 0) + ($request->special_allowance ?? 0);
            }
            
            $totalEarnings = $request->basic_salary + $totalAllowances;

            $totalDeductions = ($request->esi ?? 0) + ($request->pf ?? 0) + ($request->tds ?? 0) +
                              ($request->other_deductions ?? 0) + ($request->mobile_deduction ?? 0) + ($request->comp_off ?? 0);

            $netPayable = $totalEarnings - $totalDeductions;

            // Distribute allowances if provided as single field
            $hra = $request->hra ?? 0;
            $conveyance = $request->conveyance_allowance ?? 0;
            $medical = $request->medical_allowance ?? 0;
            $special = $request->special_allowance ?? 0;
            
            // If single allowances field is provided, distribute it equally or use individual values
            if ($request->allowances && $request->allowances > 0 && $hra == 0 && $conveyance == 0 && $medical == 0 && $special == 0) {
                // Distribute equally among allowances
                $hra = $totalAllowances / 4;
                $conveyance = $totalAllowances / 4;
                $medical = $totalAllowances / 4;
                $special = $totalAllowances / 4;
            }
            
            $payslip = Payslip::create([
                'employee_id' => $request->employee_id,
                'month' => $request->month,
                'year' => $request->year,
                'basic_salary' => $request->basic_salary,
                'hra' => $hra,
                'conveyance_allowance' => $conveyance,
                'medical_allowance' => $medical,
                'special_allowance' => $special,
                'esi' => $request->esi ?? 0,
                'pf' => $request->pf ?? 0,
                'tds' => $request->tds ?? 0,
                'other_deductions' => $request->other_deductions ?? 0,
                'mobile_deduction' => $request->mobile_deduction ?? 0,
                'comp_off' => $request->comp_off ?? 0,
                'total_earnings' => $totalEarnings,
                'total_deductions' => $totalDeductions,
                'salary_payable' => $netPayable,
                'days_payable' => $request->days_payable,
            ]);

            \App\Helpers\ActivityLogHelper::log('created', $payslip, "Accounts - Generated payslip for employee: {$payslip->employee->full_name}");

            return response()->json([
                'success' => true,
                'message' => 'Payslip generated successfully.',
                'redirect' => route('accounts.payroll.index')
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error generating payslip: ' . $e->getMessage()
            ], 500);
        }
    }

    public function show(Payslip $payslip, Request $request)
    {
        try {
            $payslip->load('employee');
            if ($request->ajax()) {
                return view('accounts.payroll.partials.view-modal', compact('payslip'))->render();
            }
            // For non-ajax requests, redirect to index
            return redirect()->route('accounts.payroll.index');
        } catch (\Exception $e) {
            if ($request->ajax()) {
                return response()->json(['error' => 'Error loading payslip: ' . $e->getMessage()], 500);
            }
            return redirect()->route('accounts.payroll.index')->with('error', 'Error loading payslip: ' . $e->getMessage());
        }
    }

    public function generatePdf(Payslip $payslip)
    {
        try {
            $payslip->load('employee');
            $pdf = PDF::loadView('accounts.payroll.pdf', compact('payslip'));
            return $pdf->download('payslip-' . $payslip->month . '-' . $payslip->year . '-' . $payslip->employee->employee_id . '.pdf');
        } catch (\Exception $e) {
            return redirect()->back()->with('error', 'Error generating PDF: ' . $e->getMessage());
        }
    }
}

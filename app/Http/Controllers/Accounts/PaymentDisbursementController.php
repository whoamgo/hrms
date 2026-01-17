<?php

namespace App\Http\Controllers\Accounts;

use App\Http\Controllers\Controller;
use App\Models\PaymentDisbursement;
use App\Models\Employee;
use App\Models\Role;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Auth;

class PaymentDisbursementController extends Controller
{
    public function index()
    {
        try {
            $roles = Role::where('is_active', true)->get();
            return view('accounts.payment-disbursement.index', compact('roles'));
        } catch (\Exception $e) {
            return redirect()->route('accounts.dashboard')->with('error', 'Error loading payment disbursement: ' . $e->getMessage());
        }
    }

    public function getEmployeesByRole(Request $request)
    {
        try {
            $roleSlug = $request->get('role');
            
            $query = Employee::where('status', 'active');
            
            // if ($roleSlug) {
            //     $role = Role::where('slug', $roleSlug)->first();
            //     if ($role) {
            //         $query->whereHas('user', function($q) use ($role) {
            //             $q->where('role_id', $role->id);
            //         });
            //     }
            // }
            
            $employees = $query->get();

          //  echo "<pre>"; print_r($employees); die()
            
            return response()->json([
                'success' => true,
                'employees' => $employees->map(function($emp) {
                    return [
                        'id' => $emp->id,
                        'name' => $emp->full_name . ' (' . $emp->employee_id . ')',
                        'employee_id' => $emp->employee_id,
                        'bank_account_number' => $emp->bank_account_number ?? 'N/A',
                        'bank_name' => $emp->bank_name ?? 'N/A',
                        'ifsc_code' => $emp->ifsc_code ?? 'N/A',
                    ];
                })
            ]);
        } catch (\Exception $e) {
            return response()->json(['success' => false, 'message' => $e->getMessage()], 500);
        }
    }

    public function getDisbursements(Request $request)
    {
        try {
            $query = PaymentDisbursement::with('employee', 'creator');

            if ($request->has('search') && $request->search['value']) {
                $search = $request->search['value'];
                $query->where(function($q) use ($search) {
                    $q->where('transaction_id', 'like', "%{$search}%")
                      ->orWhereHas('employee', function($q2) use ($search) {
                          $q2->where('full_name', 'like', "%{$search}%")
                             ->orWhere('employee_id', 'like', "%{$search}%");
                      });
                });
            }

            if ($request->filled('role')) {
                $role = Role::where('slug', $request->role)->first();
                if ($role) {
                    $query->whereHas('employee.user', function($q) use ($role) {
                        $q->where('role_id', $role->id);
                    });
                }
            }

            if ($request->filled('month')) {
                $query->where('month', $request->month);
            }

            if ($request->filled('year')) {
                $query->where('year', $request->year);
            }

            if ($request->filled('disbursement_status')) {
                $query->where('disbursement_status', $request->disbursement_status);
            }

            $totalRecords = PaymentDisbursement::count();
            $filteredRecords = $query->count();

            $orderColumn = $request->order[0]['column'] ?? 0;
            $orderDir = $request->order[0]['dir'] ?? 'desc';
            $columns = ['id', 'employee_id', 'amount', 'transaction_id', 'disbursement_status', 'created_at'];
            $orderBy = $columns[$orderColumn] ?? 'id';
            $query->orderBy($orderBy, $orderDir);

            $start = $request->start ?? 0;
            $length = $request->length ?? 10;
            $disbursements = $query->skip($start)->take($length)->get();
           // echo "<pre>"; print_r($disbursements); die();
            $data = [];
            foreach ($disbursements as $disbursement) {
                $data[] = [
                    'id' => $disbursement->id,
                    'employee_name' => $disbursement->employee->full_name ?? 'N/A',
                    'bank_account' => !empty($disbursement->employee?->bank_account_number)
                    ? 'XXXXXX' . substr($disbursement->employee->bank_account_number, -4)
                    : 'N/A',
                    'amount' => '₹' . number_format($disbursement->amount, 2),
                    'transaction_id' => $disbursement->transaction_id,
                    'month_year' => $disbursement->month . ' ' . $disbursement->year,
                    'disbursement_status' => $disbursement->disbursement_status,
                    'actions' => view('accounts.payment-disbursement.partials.actions', ['disbursement' => $disbursement])->render(),
                ];
            }

            return response()->json([
                'draw' => intval($request->draw),
                'recordsTotal' => $totalRecords,
                'recordsFiltered' => $filteredRecords,
                'data' => $data
            ]);
        } catch (\Exception $e) {
            return response()->json(['error' => 'Error loading disbursements: ' . $e->getMessage()], 500);
        }
    }

    public function show(PaymentDisbursement $disbursement, Request $request)
    {
        try {
            $disbursement->load('employee.user', 'creator');
            
            if ($request->ajax()) {
                return view('accounts.payment-disbursement.partials.view-modal', compact('disbursement'))->render();
            }
            
            return view('accounts.payment-disbursement.show', compact('disbursement'));
        } catch (\Exception $e) {
            if ($request->ajax()) {
                return response()->json(['error' => 'Error loading disbursement: ' . $e->getMessage()], 500);
            }
            return redirect()->route('accounts.payment.index')->with('error', 'Error loading disbursement: ' . $e->getMessage());
        }
    }

    public function store(Request $request)
    {
        try {
            $validator = Validator::make($request->all(), [
                'employee_id' => 'required|exists:employees,id',
                'role' => 'nullable|string',
                'amount' => 'required|numeric|min:0',
                'transaction_id' => 'required|string|unique:payment_disbursements,transaction_id',
                'month' => 'required|string',
                'year' => 'required|integer|min:2000|max:2100',
                'disbursement_status' => 'required|in:Success,Pending,Failed',
                'remarks' => 'nullable|string|max:1000',
            ], [
                'employee_id.required' => 'Employee is required.',
                'employee_id.exists' => 'Selected employee is invalid.',
                'amount.required' => 'Amount is required.',
                'amount.numeric' => 'Amount must be a valid number.',
                'amount.min' => 'Amount must be greater than or equal to 0.',
                'transaction_id.required' => 'Transaction ID is required.',
                'transaction_id.unique' => 'This transaction ID already exists.',
                'month.required' => 'Month is required.',
                'year.required' => 'Year is required.',
                'year.integer' => 'Year must be a valid number.',
                'year.min' => 'Year must be after 2000.',
                'year.max' => 'Year must be before 2100.',
                'disbursement_status.required' => 'Disbursement status is required.',
                'disbursement_status.in' => 'Invalid disbursement status.',
                'remarks.max' => 'Remarks must not exceed 1000 characters.',
            ]);

            if ($validator->fails()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Validation failed.',
                    'errors' => $validator->errors()
                ], 422);
            }

            $employee = Employee::find($request->employee_id);
            if (!$employee) {
                return response()->json([
                    'success' => false,
                    'message' => 'Employee not found.'
                ], 404);
            }

            $role = $employee->user ? $employee->user->role : null;

            $disbursement = PaymentDisbursement::create([
                'employee_id' => $request->employee_id,
                'role' => $role ? $role->slug : null,
                'amount' => $request->amount,
                'transaction_id' => $request->transaction_id,
                'month' => $request->month,
                'year' => $request->year,
                'disbursement_status' => $request->disbursement_status,
                'created_by' => Auth::id(),
                'remarks' => $request->remarks,
            ]);

            \App\Helpers\ActivityLogHelper::log('created', $disbursement, "Created payment disbursement for employee: {$employee->full_name}");

            return response()->json([
                'success' => true,
                'message' => 'Payment disbursement created successfully.',
                'redirect' => route('accounts.payment.index')
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error creating payment disbursement: ' . $e->getMessage()
            ], 500);
        }
    }
}

<?php
/*
 
 
*/


namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Employee;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class ContractRenewalController extends Controller
{
    public function index()
    {
        try {
            return view('admin.contract-renewals.index');
        } catch (\Exception $e) {
            \App\Helpers\ActivityLogHelper::log('viewed', null, 'Contract Renewal Management - Error: ' . $e->getMessage());
            return redirect()->route('admin.dashboard')->with('error', 'Error loading contract renewals: ' . $e->getMessage());
        }
    }

    public function getContractRenewals(Request $request)
    {
        try {
            $query = Employee::where('employee_type', 'Contract');

            if ($request->has('search') && $request->search['value']) {
                $search = $request->search['value'];
                $query->where(function($q) use ($search) {
                    $q->where('full_name', 'like', "%{$search}%")
                      ->orWhere('employee_id', 'like', "%{$search}%");
                });
            }

            $totalRecords = Employee::where('employee_type', 'Contract')->count();
            $filteredRecords = $query->count();

            $orderColumn = $request->order[0]['column'] ?? 0;
            $orderDir = $request->order[0]['dir'] ?? 'desc';
            $columns = ['id', 'full_name', 'contract_start_date', 'contract_end_date', 'created_at'];
            $orderBy = $columns[$orderColumn] ?? 'id';
            $query->orderBy($orderBy, $orderDir);

            $start = $request->start ?? 0;
            $length = $request->length ?? 10;
            $employees = $query->skip($start)->take($length)->get();

            $data = [];
            foreach ($employees as $employee) {
                $daysRemaining = null;
                if ($employee->contract_end_date) {
                    $endDate = Carbon::parse($employee->contract_end_date);
                    $now = Carbon::now();
                    if ($endDate->isFuture()) {
                        $daysRemaining = $now->diffInDays($endDate) . ' Days';
                    } else {
                        $daysRemaining = 'Expired';
                    }
                }

                $data[] = [
                    'id' => $employee->id,
                    'employee_name' => $employee->full_name,
                    'contract_start_date' => $employee->contract_start_date ? $employee->contract_start_date->format('d M Y') : 'N/A',
                    'contract_end_date' => $employee->contract_end_date ? $employee->contract_end_date->format('d M Y') : 'N/A',
                    'days_remaining' => $daysRemaining ?? 'N/A',
                    'actions' => view('admin.contract-renewals.partials.actions', ['employee' => $employee])->render(),
                ];
            }

            return response()->json([
                'draw' => intval($request->draw),
                'recordsTotal' => $totalRecords,
                'recordsFiltered' => $filteredRecords,
                'data' => $data
            ]);
        } catch (\Exception $e) {
            return response()->json(['error' => 'Error loading contract renewals: ' . $e->getMessage()], 500);
        }
    }

    public function show(Employee $employee)
    {
        try {
            // Get contract history from contract_history table
            $contractHistory = DB::table('contract_history')
                ->where('employee_id', $employee->id)
                ->orderBy('created_at', 'desc')
                ->get()
                ->map(function($item) {
                    return [
                        'start_date' => Carbon::parse($item->start_date)->format('d M Y'),
                        'end_date' => Carbon::parse($item->end_date)->format('d M Y'),
                        'status' => Carbon::parse($item->end_date)->isPast() ? 'Expired' : 'Completed',
                        'remarks' => $item->remarks,
                        'renewed_at' => $item->renewed_at ? Carbon::parse($item->renewed_at)->format('d M Y H:i') : 'N/A',
                    ];
                })
                ->toArray();
            
            // Add current contract if exists
            if ($employee->contract_start_date && $employee->contract_end_date) {
                array_unshift($contractHistory, [
                    'start_date' => $employee->contract_start_date->format('d M Y'),
                    'end_date' => $employee->contract_end_date->format('d M Y'),
                    'status' => Carbon::parse($employee->contract_end_date)->isPast() ? 'Expired' : 'Active',
                    'remarks' => 'Current Contract',
                    'renewed_at' => 'N/A',
                ]);
            }

            return response()->json([
                'success' => true,
                'employee' => $employee,
                'contract_history' => $contractHistory
            ]);
        } catch (\Exception $e) {
            return response()->json(['error' => 'Error loading contract details: ' . $e->getMessage()], 500);
        }
    }

    public function renew(Request $request, Employee $employee)
    {
        try {
            $validator = Validator::make($request->all(), [
                'new_start_date' => 'required|date',
                'new_end_date' => 'required|date|after:new_start_date',
                'remarks' => 'nullable|string|max:1000',
            ], [
                'new_start_date.required' => 'New start date is required.',
                'new_start_date.date' => 'New start date must be a valid date.',
                'new_end_date.required' => 'New end date is required.',
                'new_end_date.date' => 'New end date must be a valid date.',
                'new_end_date.after' => 'New end date must be after start date.',
                'remarks.max' => 'Remarks must not exceed 1000 characters.',
            ]);

            if ($validator->fails()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Validation failed.',
                    'errors' => $validator->errors()
                ], 422);
            }

            $oldValues = [
                'contract_start_date' => $employee->contract_start_date,
                'contract_end_date' => $employee->contract_end_date,
            ];

            // Save old contract to history before updating
            if ($employee->contract_start_date && $employee->contract_end_date) {
                DB::table('contract_history')->insert([
                    'employee_id' => $employee->id,
                    'start_date' => $employee->contract_start_date,
                    'end_date' => $employee->contract_end_date,
                    'remarks' => $request->remarks ?? 'Contract Renewed',
                    'renewed_by' => auth()->id(),
                    'renewed_at' => now(),
                    'created_at' => now(),
                    'updated_at' => now(),
                ]);
            }

            $employee->update([
                'contract_start_date' => $request->new_start_date,
                'contract_end_date' => $request->new_end_date,
            ]);

            \App\Helpers\ActivityLogHelper::log(
                'updated',
                $employee,
                "Renewed contract for {$employee->full_name}",
                $oldValues,
                ['contract_start_date' => $request->new_start_date, 'contract_end_date' => $request->new_end_date]
            );

            return response()->json([
                'success' => true,
                'message' => 'Contract renewed successfully. Previous contract saved to history.',
                'redirect' => route('admin.contract-renewals.index')
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error renewing contract: ' . $e->getMessage()
            ], 500);
        }
    }

    public function close(Employee $employee)
    {
        try {
            $oldValues = [
                'contract_start_date' => $employee->contract_start_date,
                'contract_end_date' => $employee->contract_end_date,
                'status' => $employee->status,
            ];

            $employee->update([
                'contract_start_date' => null,
                'contract_end_date' => null,
                'employee_type' => 'Permanent', // Or set status to inactive
            ]);

            \App\Helpers\ActivityLogHelper::log(
                'updated',
                $employee,
                "Closed contract for {$employee->full_name}",
                $oldValues,
                ['contract_start_date' => null, 'contract_end_date' => null]
            );

            return response()->json([
                'success' => true,
                'message' => 'Contract closed successfully.'
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error closing contract: ' . $e->getMessage()
            ], 500);
        }
    }
}

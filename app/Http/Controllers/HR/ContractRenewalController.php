<?php

namespace App\Http\Controllers\HR;

use App\Http\Controllers\Controller;
use App\Models\Employee;
use App\Models\ServiceRecord;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;
use Illuminate\Support\Facades\Crypt;
class ContractRenewalController extends Controller
{
    public function index()
    {
        try {
            \App\Helpers\ActivityLogHelper::log('viewed', null, 'HR - Contract Renewal Management');
            return view('hr.contract-renewals.index');
        } catch (\Exception $e) {
            return redirect()->route('hr.dashboard')->with('error', 'Error loading contract renewals: ' . $e->getMessage());
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
                 
               $daysRemaining = 'N/A';

               if ($employee->contract_end_date) {

                   $today = Carbon::now()->startOfDay();
                   $endDate = Carbon::parse($employee->contract_end_date)->startOfDay();

                   if ($endDate->gte($today)) {
                       $daysRemaining = ($today->diffInDays($endDate) + 1) . ' Days';
                   } else {
                       $daysRemaining = 'Expired';
                   }
               }

                $data[] = [
                    'id' => $employee->id,
                    'employee_name' => $employee->full_name,
                    'contract_start_date' => $employee->contract_start_date ? $employee->contract_start_date->format('d M Y') : 'N/A',
                    'contract_end_date' => $employee->contract_end_date ? $employee->contract_end_date->format('d M Y') : 'N/A',
                    'days_remaining' => $employee->contract_end_date
                        ? (Carbon::parse($employee->contract_end_date)->isFuture()
                            ? Carbon::now()->diffInDays($employee->contract_end_date) . ' Days'
                            : 'Expired')
                        : 'N/A',
                    'actions' => view('hr.contract-renewals.partials.actions', ['employee' => $employee])->render(),
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
            $contractHistory = \DB::table('contract_history')
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

    public function renew(Request $request)
    {
        try {
            // 1️⃣ Validate input
            $validator = Validator::make($request->all(), [
                'employee_id'     => 'required',
                'new_start_date'  => 'required|date',
                'new_end_date'    => 'required|date|after:new_start_date',
                'remarks'         => 'nullable|string',
            ]);

            if ($validator->fails()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Validation failed.',
                    'errors'  => $validator->errors()
                ], 422);
            }

            // 2️⃣ Decrypt employee ID
            try {
                $employeeId = Crypt::decryptString($request->employee_id);
            } catch (\Exception $e) {
                return response()->json([
                    'success' => false,
                    'message' => 'Invalid employee ID.'
                ], 400);
            }

            // 3️⃣ Fetch employee
            $employee = Employee::find($employeeId);

            if (!$employee) {
                return response()->json([
                    'success' => false,
                    'message' => 'Employee not found.'
                ], 404);
            }

            // 4️⃣ Store old values for activity log
            $oldValues = [
                'contract_start_date' => $employee->contract_start_date,
                'contract_end_date'   => $employee->contract_end_date,
            ];

            // 5️⃣ Save previous contract to service records
            if ($employee->contract_start_date && $employee->contract_end_date) {
                ServiceRecord::create([
                    'employee_id' => $employee->id,
                    'from_date'   => $employee->contract_start_date,
                    'to_date'     => $employee->contract_end_date,
                    'designation' => $employee->designation,
                    'department'  => $employee->department,
                    'remarks'     => 'Contract Renewal - ' . ($request->remarks ?? 'Renewed'),
                    'status'      => 'inactive',
                ]);
            }

            // 6️⃣ Update employee contract
            $employee->update([
                'contract_start_date' => $request->new_start_date,
                'contract_end_date'   => $request->new_end_date,
            ]);

            // 7️⃣ Activity log
            \App\Helpers\ActivityLogHelper::log(
                'updated',
                $employee,
                "HR - Renewed contract for {$employee->full_name}",
                $oldValues,
                [
                    'contract_start_date' => $request->new_start_date,
                    'contract_end_date'   => $request->new_end_date
                ]
            );

            return response()->json([
                'success'  => true,
                'message'  => 'Contract renewed successfully.',
                'redirect' => route('hr.contract-renewals.index')
            ]);

        } catch (\Exception $e) {

            Log::error('Contract renewal failed', [
                'error' => $e->getMessage()
            ]);

            return response()->json([
                'success' => false,
                'message' => 'Error renewing contract.'
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

            // Save to contract history before closing
            if ($employee->contract_start_date && $employee->contract_end_date) {
                DB::table('contract_history')->insert([
                    'employee_id' => $employee->id,
                    'start_date' => $employee->contract_start_date,
                    'end_date' => $employee->contract_end_date,
                    'remarks' => 'Contract Closed',
                    'renewed_by' => auth()->id(),
                    'renewed_at' => now(),
                    'created_at' => now(),
                    'updated_at' => now(),
                ]);
            }

            $employee->update([
                'contract_start_date' => null,
                'contract_end_date' => null,
                'employee_type' => 'Permanent',
                'status' => 'inactive',
            ]);

            // Deactivate user account
            if ($employee->user_id) {
                $user = \App\Models\User::find($employee->user_id);
                if ($user) {
                    $user->update(['status' => 'inactive']);
                }
            }

            \App\Helpers\ActivityLogHelper::log(
                'updated',
                $employee,
                "HR - Closed contract for {$employee->full_name}",
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

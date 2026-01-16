<?php

namespace App\Http\Controllers\HR;

use App\Http\Controllers\Controller;
use App\Models\Leave;
use App\Models\Employee;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Validator;
use Carbon\Carbon;
use App\Helpers\NotificationHelper;
use Illuminate\Support\Facades\Crypt;
class LeaveController extends Controller
{
    public function index()
    {
        try {
            $employees = Cache::remember('all_employees', 3600, function() {
                return Employee::where('status', 'active')->get();
            });
            
            $leaveTypes = ['CL' => 'Casual Leave (CL)', 'SL' => 'Sick Leave (SL)', 'SPL' => 'Special Leave'];

            return view('hr.leaves.index', compact('employees', 'leaveTypes'));
        } catch (\Exception $e) {
            return redirect()->back()->with('error', 'Error loading leaves: ' . $e->getMessage());
        }
    }

    public function getLeaves(Request $request)
    {
        try {
            $query = Leave::with('employee');

            if ($request->has('search') && $request->search['value']) {
                $search = $request->search['value'];
                $query->where(function($q) use ($search) {
                    $q->whereHas('employee', function($q2) use ($search) {
                        $q2->where('full_name', 'like', "%{$search}%")
                           ->orWhere('employee_id', 'like', "%{$search}%");
                    })
                    ->orWhere('leave_type', 'like', "%{$search}%");
                });
            }

            if ($request->filled('employee_id')) {
                $query->whereHas('employee', function($q) use ($request) {
                    $q->where('full_name', 'like', "%{$request->employee_id}%")
                      ->orWhere('employee_id', 'like', "%{$request->employee_id}%");
                });
            }

            if ($request->filled('leave_type')) {
                $query->where('leave_type', $request->leave_type);
            }

            if ($request->filled('from_date')) {
                $query->where('from_date', '>=', $request->from_date);
            }

            if ($request->filled('to_date')) {
                $query->where('to_date', '<=', $request->to_date);
            }

            if ($request->filled('status')) {
                $query->where('status', $request->status);
            }

            $totalRecords = Leave::count();
            $filteredRecords = $query->count();

            $orderColumn = $request->order[0]['column'] ?? 0;
            $orderDir = $request->order[0]['dir'] ?? 'desc';
            $columns = ['id', 'employee_id', 'leave_type', 'from_date', 'status', 'created_at'];
            $orderBy = $columns[$orderColumn] ?? 'id';
            $query->orderBy($orderBy, $orderDir);

            $start = $request->start ?? 0;
            $length = $request->length ?? 10;
            $leaves = $query->skip($start)->take($length)->get();

            $data = [];
            foreach ($leaves as $leave) {
                $dates = $leave->from_date->format('d-m-Y') . ' to ' . $leave->to_date->format('d-m-Y');
                $leaveTypeName = $leave->leave_type == 'CL' ? 'Casual Leave (CL)' : ($leave->leave_type == 'SL' ? 'Sick Leave (SL)' : 'Special Leave');

                $data[] = [
                    'id' => $leave->id,
                    'employee_name' => $leave->employee->full_name,
                    'leave_type' => $leaveTypeName,
                    'dates' => $dates,
                    'status' => $leave->status,
                    'actions' => view('hr.leaves.partials.actions', ['leave' => $leave])->render(),
                ];
            }

            return response()->json([
                'draw' => intval($request->draw),
                'recordsTotal' => $totalRecords,
                'recordsFiltered' => $filteredRecords,
                'data' => $data
            ]);
        } catch (\Exception $e) {
            return response()->json(['error' => 'Error loading leaves: ' . $e->getMessage()], 500);
        }
    }

    public function create()
    {
        try {
            $employees = Cache::remember('all_employees', 3600, function() {
                return Employee::where('status', 'active')->get();
            });
            return view('hr.leaves.create', compact('employees'));
        } catch (\Exception $e) {
            return redirect()->route('hr.leaves.index')->with('error', 'Error loading form: ' . $e->getMessage());
        }
    }

    public function store(Request $request)
    {
        try {
            $validator = Validator::make($request->all(), [
                'employee_id' => 'required|exists:employees,id',
                'leave_type' => 'required|in:CL,SL,SPL',
                'from_date' => 'required|date',
                'to_date' => 'required|date|after_or_equal:from_date',
                'reason' => 'nullable|string',
            ]);

            if ($validator->fails()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Validation failed.',
                    'errors' => $validator->errors()
                ], 422);
            }

            $fromDate = Carbon::parse($request->from_date);
            $toDate = Carbon::parse($request->to_date);
            
            // Calculate total days based on day_type
            $dayType = $request->day_type ?? 'Full';
            if ($dayType == 'Half') {
                $totalDays = 0.5;
            } else {
                $totalDays = $fromDate->diffInDays($toDate) + 1;
            }

            Leave::create([
                'employee_id' => $request->employee_id,
                'leave_type' => $request->leave_type,
                'day_type' => $dayType,
                'from_date' => $request->from_date,
                'to_date' => $request->to_date,
                'total_days' => $totalDays,
                'subject' => $request->subject ?? $request->reason,
                'message' => $request->message ?? $request->reason,
                'reason' => $request->reason ?? $request->message,
                'status' => 'pending',
            ]);

            // notify()
            // ->success()
            // ->title('⚡️ Laravel Notify is awesome!')
            // ->send();

            return response()->json([
                'success' => true,
                'message' => 'Leave created successfully.',
                'redirect' => route('hr.leaves.index')
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error creating leave: ' . $e->getMessage()
            ], 500);
        }
    }


    public function show(Request $request, $id)
    {
        try {
            $leave = Leave::with(['employee', 'approver'])->find(Crypt::decryptString($id));

            if (!$leave) {
                if ($request->ajax()) {
                    return response()->json([
                        'error' => 'Leave record not found.'
                    ], 404);
                }

                return redirect()
                    ->route('hr.leaves.index')
                    ->with('error', 'Leave record not found.');
            }

            // AJAX request → modal view
            if ($request->ajax()) {
                return view(
                    'hr.leaves.partials.view-modal',
                    compact('leave')
                );
            }

            // Normal page view
            return view('hr.leaves.show', compact('leave'));

        } catch (\Exception $e) {

            Log::error('Error loading leave', [
                'leave_id' => $id,
                'message'  => $e->getMessage()
            ]);

            if ($request->ajax()) {
                return response()->json([
                    'error' => 'Error loading leave details.'
                ], 500);
            }

            return redirect()
                ->route('hr.leaves.index')
                ->with('error', 'Error loading leave details.');
        }
    }


     public function edit($id)
     {
         try {
             $leave = Leave::find(Crypt::decryptString($id));

             if (!$leave) {
                 return redirect()
                     ->route('hr.leaves.index')
                     ->with('error', 'Leave record not found.');
             }

             $employees = Cache::remember('all_employees', 3600, function () {
                 return Employee::where('status', 'active')->get();
             });

             return view('hr.leaves.edit', compact('leave', 'employees'));

         } catch (\Exception $e) {
             return redirect()
                 ->route('hr.leaves.index')
                 ->with('error', 'Error loading form.');
         }
     }


    public function update(Request $request, Leave $leave)
    {
        try {
            $validator = Validator::make($request->all(), [
                'employee_id' => 'required|exists:employees,id',
                'leave_type' => 'required|in:CL,SL,SPL',
                'from_date' => 'required|date',
                'to_date' => 'required|date|after_or_equal:from_date',
                'reason' => 'nullable|string',
            ]);

            if ($validator->fails()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Validation failed.',
                    'errors' => $validator->errors()
                ], 422);
            }

            $fromDate = Carbon::parse($request->from_date);
            $toDate = Carbon::parse($request->to_date);
            
            // Calculate total days based on day_type
            $dayType = $request->day_type ?? $leave->day_type ?? 'Full';
            if ($dayType == 'Half') {
                $totalDays = 0.5;
            } else {
                $totalDays = $fromDate->diffInDays($toDate) + 1;
            }

            $leave->update([
                'employee_id' => $request->employee_id,
                'leave_type' => $request->leave_type,
                'day_type' => $dayType,
                'from_date' => $request->from_date,
                'to_date' => $request->to_date,
                'total_days' => $totalDays,
                'subject' => $request->subject ?? $request->reason ?? $leave->subject,
                'message' => $request->message ?? $request->reason ?? $leave->message,
                'reason' => $request->reason ?? $request->message ?? $leave->reason,
            ]);

            return response()->json([
                'success' => true,
                'message' => 'Leave updated successfully.',
                'redirect' => route('hr.leaves.index')
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error updating leave: ' . $e->getMessage()
            ], 500);
        }
    }

    public function destroy(Leave $leave)
    {
        try {
            $leave->delete();
            return response()->json([
                'success' => true,
                'message' => 'Leave deleted successfully.'
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error deleting leave: ' . $e->getMessage()
            ], 500);
        }
    }

    public function approve(Leave $leave)
    {
        try {
            $leave->update([
                'status' => 'approved',
                'approved_by' => auth()->id(),
                'approved_at' => now(),
            ]);

            // Notify Employee about leave approval
            if ($leave->employee && $leave->employee->user) {
                NotificationHelper::notify(
                    $leave->employee->user_id,
                    'Leave Approved',
                    "Your {$leave->leave_type} leave from {$leave->from_date->format('d M Y')} to {$leave->to_date->format('d M Y')} has been approved.",
                    'success',
                    route('employee.leaves.create')
                );
            }

            // Notify Admin
            NotificationHelper::notifyByRole(
                'admin',
                'Leave Approved',
                "HR approved leave for {$leave->employee->full_name} ({$leave->leave_type}) from {$leave->from_date->format('d M Y')} to {$leave->to_date->format('d M Y')}.",
                'info',
                route('admin.leaves.index')
            );

            \App\Helpers\ActivityLogHelper::log('updated', $leave, "HR - Approved leave for employee: {$leave->employee->full_name}");

            return response()->json([
                'success' => true,
                'message' => 'Leave approved successfully.'
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error approving leave: ' . $e->getMessage()
            ], 500);
        }
    }

    public function reject(Request $request, Leave $leave)
    {
        try {
            $validator = Validator::make($request->all(), [
                'rejection_reason' => 'required|string',
            ]);

            if ($validator->fails()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Rejection reason is required.',
                    'errors' => $validator->errors()
                ], 422);
            }

            $leave->update([
                'status' => 'rejected',
                'rejection_reason' => $request->rejection_reason,
            ]);

            // Notify Employee about leave rejection
            if ($leave->employee && $leave->employee->user) {
                NotificationHelper::notify(
                    $leave->employee->user_id,
                    'Leave Rejected',
                    "Your {$leave->leave_type} leave from {$leave->from_date->format('d M Y')} to {$leave->to_date->format('d M Y')} has been rejected. Reason: {$request->rejection_reason}",
                    'error',
                    route('employee.leaves.create')
                );
            }

            // Notify Admin
            NotificationHelper::notifyByRole(
                'admin',
                'Leave Rejected',
                "HR rejected leave for {$leave->employee->full_name} ({$leave->leave_type}) from {$leave->from_date->format('d M Y')} to {$leave->to_date->format('d M Y')}.",
                'warning',
                route('admin.leaves.index')
            );

            \App\Helpers\ActivityLogHelper::log('updated', $leave, "HR - Rejected leave for employee: {$leave->employee->full_name}");

            return response()->json([
                'success' => true,
                'message' => 'Leave rejected successfully.'
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error rejecting leave: ' . $e->getMessage()
            ], 500);
        }
    }

    public function autocompleteEmployeeName(Request $request)
    {
        try {
            $term = $request->get('term', '');
            $names = Employee::where('full_name', 'like', "%{$term}%")
                ->where('status', 'active')
                ->pluck('full_name')
                ->take(10)
                ->toArray();
            return response()->json($names);
        } catch (\Exception $e) {
            return response()->json([], 500);
        }
    }
}

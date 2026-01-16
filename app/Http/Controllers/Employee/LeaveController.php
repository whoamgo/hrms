<?php

namespace App\Http\Controllers\Employee;

use App\Http\Controllers\Controller;
use App\Models\Leave;
use App\Models\Employee;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Auth;
use Carbon\Carbon;
use App\Helpers\NotificationHelper;

class LeaveController extends Controller
{
    /**
     * Show the form for creating a new leave application.
     */
    public function create()
    {
        try {
            $user = Auth::user();
            $employee = $user->employee;
            
            if (!$employee) {
                return redirect()->route('employee.dashboard')->with('error', 'Employee record not found.');
            }

            return view('employee.leaves.create', compact('employee'));
        } catch (\Exception $e) {
            return redirect()->route('employee.dashboard')->with('error', 'Error loading form: ' . $e->getMessage());
        }
    }

    /**
     * Store a newly created leave application.
     */
    public function store(Request $request)
    {
        try {
            $user = Auth::user();
            $employee = $user->employee;
            
            if (!$employee) {
                return response()->json([
                    'success' => false,
                    'message' => 'Employee record not found.'
                ], 404);
            }

            $validator = Validator::make($request->all(), [
                'leave_type' => 'required|in:CL,SL,SPL',
                'day_type' => 'required|in:Full,Half',
                'from_date' => 'required|date',
                'to_date' => 'required|date|after_or_equal:from_date',
                'subject' => 'required|string|max:255',
                'message' => 'nullable|string',
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
            if ($request->day_type == 'Half') {
                $totalDays = 0.5;
            } else {
                $totalDays = $fromDate->diffInDays($toDate) + 1;
            }

            $leave = Leave::create([
                'employee_id' => $employee->id,
                'leave_type' => $request->leave_type,
                'day_type' => $request->day_type,
                'from_date' => $request->from_date,
                'to_date' => $request->to_date,
                'total_days' => $totalDays,
                'subject' => $request->subject,
                'message' => $request->message ?? $request->subject,
                'reason' => $request->message,
                'status' => 'pending',
            ]);

            // Notify HR about new leave application
            NotificationHelper::notifyByRole(
                'hr',
                'New Leave Application',
                "{$employee->full_name} has applied for {$request->leave_type} leave from {$fromDate->format('d M Y')} to {$toDate->format('d M Y')}.",
                'info',
                route('hr.leaves.index')
            );

            // Notify Admin about new leave application
            NotificationHelper::notifyByRole(
                'admin',
                'New Leave Application',
                "{$employee->full_name} has applied for {$request->leave_type} leave from {$fromDate->format('d M Y')} to {$toDate->format('d M Y')}.",
                'info',
                route('admin.leaves.index')
            );

            return response()->json([
                'success' => true,
                'message' => 'Leave application submitted successfully.',
                'redirect' => route('employee.leaves.create')
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error submitting leave application: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Get employee's leave status (for DataTable)
     */
    public function getMyLeaves(Request $request)
    {
        try {
            $user = Auth::user();
            $employee = $user->employee;
            
            if (!$employee) {
                return response()->json(['error' => 'Employee record not found.'], 404);
            }

            $query = Leave::where('employee_id', $employee->id);

            if ($request->has('search') && $request->search['value']) {
                $search = $request->search['value'];
                $query->where(function($q) use ($search) {
                    $q->where('subject', 'like', "%{$search}%")
                      ->orWhere('leave_type', 'like', "%{$search}%");
                });
            }

            $totalRecords = Leave::where('employee_id', $employee->id)->count();
            $filteredRecords = $query->count();

            $orderColumn = $request->order[0]['column'] ?? 0;
            $orderDir = $request->order[0]['dir'] ?? 'desc';
            $columns = ['id', 'from_date', 'to_date', 'leave_type', 'status', 'created_at'];
            $orderBy = $columns[$orderColumn] ?? 'id';
            $query->orderBy($orderBy, $orderDir);

            $start = $request->start ?? 0;
            $length = $request->length ?? 10;
            $leaves = $query->skip($start)->take($length)->get();

            $data = [];
            foreach ($leaves as $leave) {
                $leaveTypeName = $leave->leave_type == 'CL' ? 'Casual Leave (CL)' : ($leave->leave_type == 'SL' ? 'Sick Leave (SL)' : 'Special Leave');
                
                $data[] = [
                    'id' => $leave->id,
                    'name' => $employee->full_name,
                    'from_date' => $leave->from_date->format('d M Y'),
                    'to_date' => $leave->to_date->format('d M Y'),
                    'leave_type' => $leave->day_type ?? 'Full',
                    'days' => $leave->total_days,
                    'apply_for' => 'Leave',
                    'subject' => $leave->subject ?? 'N/A',
                    'message' => $leave->message ?? $leave->subject ?? 'N/A',
                    'status' => $leave->status,
                    'reason' => $leave->rejection_reason ?? 'Other Reason',
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
}

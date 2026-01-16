<?php
//
namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Attendance;
use App\Models\Employee;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Validator;
use Carbon\Carbon;

class AttendanceController extends Controller
{
    public function index()
    {
        try {
            $employees = Cache::remember('all_employees', 3600, function() {
                return Employee::where('status', 'active')->get();
            });

            return view('admin.attendances.index', compact('employees'));
        } catch (\Exception $e) {
            return redirect()->back()->with('error', 'Error loading attendance: ' . $e->getMessage());
        }
    }

    public function getAttendances(Request $request)
    {
        try {
            $query = Attendance::with('employee');

            if ($request->has('search') && $request->search['value']) {
                $search = $request->search['value'];
                $query->where(function($q) use ($search) {
                    $q->whereHas('employee', function($q2) use ($search) {
                        $q2->where('full_name', 'like', "%{$search}%")
                           ->orWhere('employee_id', 'like', "%{$search}%");
                    });
                });
            }

            if ($request->filled('employee_id')) {
                $query->whereHas('employee', function($q) use ($request) {
                    $q->where('full_name', 'like', "%{$request->employee_id}%")
                      ->orWhere('employee_id', 'like', "%{$request->employee_id}%");
                });
            }

            if ($request->filled('date')) {
                $query->where('date', $request->date);
            }

            if ($request->filled('status')) {
                $query->where('status', $request->status);
            }

            $totalRecords = Attendance::count();
            $filteredRecords = $query->count();

            $orderColumn = $request->order[0]['column'] ?? 0;
            $orderDir = $request->order[0]['dir'] ?? 'desc';
            $columns = ['id', 'date', 'employee_id', 'status', 'check_in', 'created_at'];
            $orderBy = $columns[$orderColumn] ?? 'id';
            $query->orderBy($orderBy, $orderDir);

            $start = $request->start ?? 0;
            $length = $request->length ?? 10;
            $attendances = $query->skip($start)->take($length)->get();

            $data = [];
            foreach ($attendances as $attendance) {
                $date = Carbon::parse($attendance->date);
                $day = $date->format('l');
                if ($attendance->status == 'weekend' || $attendance->status == 'holiday') {
                    $day .= ' (' . ucfirst($attendance->status) . ')';
                }

                // Format check_in and check_out (they're stored as time strings)
                $checkIn = $attendance->check_in ? (is_string($attendance->check_in) ? $attendance->check_in : Carbon::parse($attendance->check_in)->format('H:i:s')) : '-';
                $checkOut = $attendance->check_out ? (is_string($attendance->check_out) ? $attendance->check_out : Carbon::parse($attendance->check_out)->format('H:i:s')) : '-';
                $workingHours = $attendance->calculateWorkingHours();

                $data[] = [
                    'id' => $attendance->id,
                    'date' => $attendance->date->format('d/m/Y'),
                    'employee_name' => $attendance->employee->full_name,
                    'day' => $day,
                    'check_in' => $checkIn,
                    'check_out' => $checkOut,
                    'working_hours' => $workingHours,
                    'status' => $attendance->status,
                    'actions' => view('admin.attendances.partials.actions', ['attendance' => $attendance])->render(),
                ];
            }

            return response()->json([
                'draw' => intval($request->draw),
                'recordsTotal' => $totalRecords,
                'recordsFiltered' => $filteredRecords,
                'data' => $data
            ]);
        } catch (\Exception $e) {
            return response()->json(['error' => 'Error loading attendance: ' . $e->getMessage()], 500);
        }
    }

    public function create()
    {
        try {
            $employees = Cache::remember('all_employees', 3600, function() {
                return Employee::where('status', 'active')->get();
            });
            return view('admin.attendances.create', compact('employees'));
        } catch (\Exception $e) {
            return redirect()->route('admin.attendances.index')->with('error', 'Error loading form: ' . $e->getMessage());
        }
    }

    public function store(Request $request)
    {
        try {
            $validator = Validator::make($request->all(), [
                'employee_id' => 'required|exists:employees,id',
                'date' => 'required|date',
                'check_in' => 'nullable|date_format:H:i',
                'check_out' => 'nullable|date_format:H:i|after:check_in',
                'status' => 'required|in:present,absent,holiday,weekend',
                'remarks' => 'nullable|string',
            ], [
                'employee_id.required' => 'Employee is required.',
                'date.required' => 'Date is required.',
                'date.unique' => 'Attendance for this employee on this date already exists.',
            ]);

            // Check if attendance already exists for this employee and date
            $existing = Attendance::where('employee_id', $request->employee_id)
                ->where('date', $request->date)
                ->first();

            if ($existing) {
                return response()->json([
                    'success' => false,
                    'message' => 'Attendance for this employee on this date already exists.',
                    'errors' => ['date' => ['Attendance already exists for this date.']]
                ], 422);
            }

            if ($validator->fails()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Validation failed.',
                    'errors' => $validator->errors()
                ], 422);
            }

            $data = $request->only(['employee_id', 'date', 'check_in', 'check_out', 'status', 'remarks']);
            
            // Format check_in and check_out as time strings (H:i:s format)
            if ($request->check_in) {
                $data['check_in'] = $request->check_in . ':00'; // Add seconds if not provided
            }
            if ($request->check_out) {
                $data['check_out'] = $request->check_out . ':00'; // Add seconds if not provided
            }
            
            // Calculate working hours if check_in and check_out are provided
            if ($request->check_in && $request->check_out) {
                try {
                    $checkIn = Carbon::createFromFormat('H:i:s', $data['check_in']);
                    $checkOut = Carbon::createFromFormat('H:i:s', $data['check_out']);
                    
                    // If check_out is before check_in, assume it's next day
                    if ($checkOut->lt($checkIn)) {
                        $checkOut->addDay();
                    }
                    
                    $diff = $checkIn->diff($checkOut);
                    $data['working_hours'] = $diff->h . ' Hrs:' . str_pad($diff->i, 2, '0', STR_PAD_LEFT) . ' Min';
                } catch (\Exception $e) {
                    // If calculation fails, set to null
                    $data['working_hours'] = null;
                }
            }

            Attendance::create($data);

            return response()->json([
                'success' => true,
                'message' => 'Attendance created successfully.',
                'redirect' => route('admin.attendances.index')
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error creating attendance: ' . $e->getMessage()
            ], 500);
        }
    }

    public function show(Attendance $attendance, Request $request)
    {
        try {
            $attendance->load('employee');
            if ($request->ajax()) {
                return view('admin.attendances.partials.view-modal', compact('attendance'))->render();
            }
            return view('admin.attendances.show', compact('attendance'));
        } catch (\Exception $e) {
            if ($request->ajax()) {
                return response()->json(['error' => 'Error loading attendance: ' . $e->getMessage()], 500);
            }
            return redirect()->route('admin.attendances.index')->with('error', 'Error loading attendance: ' . $e->getMessage());
        }
    }


    // public function show(Request $request, $id)
    // {
    //     try {
    //         $attendance = Attendance::with('employee')->find($id);

    //         if (!$attendance) {
    //             if ($request->ajax()) {
    //                 return response()->json([
    //                     'error' => 'Attendance record not found'
    //                 ], 404);
    //             }

    //             return redirect()
    //                 ->route('admin.attendances.index')
    //                 ->with('error', 'Attendance record not found');
    //         }

    //         if ($request->ajax()) {
    //             return view(
    //                 'admin.attendances.partials.view-modal',
    //                 compact('attendance')
    //             );
    //         }

    //         return view('admin.attendances.show', compact('attendance'));

    //     } catch (\Exception $e) {
    //         if ($request->ajax()) {
    //             return response()->json([
    //                 'error' => 'Error loading attendance'
    //             ], 500);
    //         }

    //         return redirect()
    //             ->route('admin.attendances.index')
    //             ->with('error', 'Error loading attendance');
    //     }
    // }


    public function edit(Attendance $attendance)
    {
        try {
            $employees = Cache::remember('all_employees', 3600, function() {
                return Employee::where('status', 'active')->get();
            });
            return view('admin.attendances.edit', compact('attendance', 'employees'));
        } catch (\Exception $e) {
            return redirect()->route('admin.attendances.index')->with('error', 'Error loading form: ' . $e->getMessage());
        }
    }

    public function update(Request $request, Attendance $attendance)
    {
        try {
            $validator = Validator::make($request->all(), [
                'employee_id' => 'required|exists:employees,id',
                'date' => 'required|date',
                'check_in' => 'nullable|date_format:H:i',
                'check_out' => 'nullable|date_format:H:i|after:check_in',
                'status' => 'required|in:present,absent,holiday,weekend',
                'remarks' => 'nullable|string',
            ]);

            // Check if attendance already exists for this employee and date (excluding current record)
            $existing = Attendance::where('employee_id', $request->employee_id)
                ->where('date', $request->date)
                ->where('id', '!=', $attendance->id)
                ->first();

            if ($existing) {
                return response()->json([
                    'success' => false,
                    'message' => 'Attendance for this employee on this date already exists.',
                    'errors' => ['date' => ['Attendance already exists for this date.']]
                ], 422);
            }

            if ($validator->fails()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Validation failed.',
                    'errors' => $validator->errors()
                ], 422);
            }

            $data = $request->only(['employee_id', 'date', 'check_in', 'check_out', 'status', 'remarks']);
            
            // Format check_in and check_out as time strings (H:i:s format)
            if ($request->check_in) {
                $data['check_in'] = $request->check_in . ':00'; // Add seconds if not provided
            } else {
                $data['check_in'] = null;
            }
            
            if ($request->check_out) {
                $data['check_out'] = $request->check_out . ':00'; // Add seconds if not provided
            } else {
                $data['check_out'] = null;
            }
            
            // Calculate working hours if check_in and check_out are provided
            if ($request->check_in && $request->check_out) {
                try {
                    $checkIn = Carbon::createFromFormat('H:i:s', $data['check_in']);
                    $checkOut = Carbon::createFromFormat('H:i:s', $data['check_out']);
                    
                    // If check_out is before check_in, assume it's next day
                    if ($checkOut->lt($checkIn)) {
                        $checkOut->addDay();
                    }
                    
                    $diff = $checkIn->diff($checkOut);
                    $data['working_hours'] = $diff->h . ' Hrs:' . str_pad($diff->i, 2, '0', STR_PAD_LEFT) . ' Min';
                } catch (\Exception $e) {
                    // If calculation fails, set to null
                    $data['working_hours'] = null;
                }
            } else {
                $data['working_hours'] = null;
            }

            $attendance->update($data);

            return response()->json([
                'success' => true,
                'message' => 'Attendance updated successfully.',
                'redirect' => route('admin.attendances.index')
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error updating attendance: ' . $e->getMessage()
            ], 500);
        }
    }

    public function destroy(Attendance $attendance)
    {
        try {
            $attendance->delete();
            return response()->json([
                'success' => true,
                'message' => 'Attendance deleted successfully.'
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error deleting attendance: ' . $e->getMessage()
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

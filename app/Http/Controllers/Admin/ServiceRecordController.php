<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\ServiceRecord;
use App\Models\Employee;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Validator;

class ServiceRecordController extends Controller
{
    public function index()
    {
        try {
            $employees = Cache::remember('all_employees', 3600, function() {
                return Employee::where('status', 'active')->get();
            });
            
            $departments = Cache::remember('service_record_departments', 3600, function() {
                return ServiceRecord::whereNotNull('department')
                    ->distinct()
                    ->pluck('department')
                    ->sort()
                    ->values();
            });

            return view('admin.service-records.index', compact('employees', 'departments'));
        } catch (\Exception $e) {
            return redirect()->back()->with('error', 'Error loading service records: ' . $e->getMessage());
        }
    }

    public function getServiceRecords(Request $request)
    {
        try {
            $query = ServiceRecord::with('employee');

            if ($request->has('search') && $request->search['value']) {
                $search = $request->search['value'];
                $query->where(function($q) use ($search) {
                    $q->whereHas('employee', function($q2) use ($search) {
                        $q2->where('full_name', 'like', "%{$search}%")
                           ->orWhere('employee_id', 'like', "%{$search}%");
                    })
                    ->orWhere('designation', 'like', "%{$search}%")
                    ->orWhere('department', 'like', "%{$search}%");
                });
            }

            if ($request->filled('employee_id')) {
                $query->whereHas('employee', function($q) use ($request) {
                    $q->where('full_name', 'like', "%{$request->employee_id}%")
                      ->orWhere('employee_id', 'like', "%{$request->employee_id}%");
                });
            }

            if ($request->filled('from_date')) {
                $query->where('from_date', '>=', $request->from_date);
            }

            if ($request->filled('to_date')) {
                $query->where('to_date', '<=', $request->to_date);
            }

            if ($request->filled('designation')) {
                $query->where('designation', 'like', "%{$request->designation}%");
            }

            if ($request->filled('department')) {
                $query->where('department', 'like', "%{$request->department}%");
            }

            $totalRecords = ServiceRecord::count();
            $filteredRecords = $query->count();

            $orderColumn = $request->order[0]['column'] ?? 0;
            $orderDir = $request->order[0]['dir'] ?? 'desc';
            $columns = ['id', 'from_date', 'designation', 'department', 'created_at'];
            $orderBy = $columns[$orderColumn] ?? 'id';
            $query->orderBy($orderBy, $orderDir);

            $start = $request->start ?? 0;
            $length = $request->length ?? 10;
            $records = $query->skip($start)->take($length)->get();

            $data = [];
            foreach ($records as $record) {
                $period = $record->from_date->format('d-m-Y');
                if ($record->to_date) {
                    $period .= ' to ' . $record->to_date->format('d-m-Y');
                } else {
                    $period .= ' to Present';
                }

                $data[] = [
                    'id' => $record->id,
                    'period' => $period,
                    'designation' => $record->designation,
                    'department' => $record->department,
                    'remarks' => $record->remarks ?? 'N/A',
                    'actions' => view('admin.service-records.partials.actions', ['record' => $record])->render(),
                ];
            }

            return response()->json([
                'draw' => intval($request->draw),
                'recordsTotal' => $totalRecords,
                'recordsFiltered' => $filteredRecords,
                'data' => $data
            ]);
        } catch (\Exception $e) {
            return response()->json(['error' => 'Error loading service records: ' . $e->getMessage()], 500);
        }
    }

    public function create()
    {
        try {
            $employees = Cache::remember('all_employees', 3600, function() {
                return Employee::where('status', 'active')->get();
            });
            return view('admin.service-records.create', compact('employees'));
        } catch (\Exception $e) {
            return redirect()->route('admin.service-records.index')->with('error', 'Error loading form: ' . $e->getMessage());
        }
    }

    public function store(Request $request)
    {
        try {
            $validator = Validator::make($request->all(), [
                'employee_id' => 'required|exists:employees,id',
                'from_date' => 'required|date',
                'to_date' => 'nullable|date|after_or_equal:from_date',
                'designation' => 'required|string|max:255',
                'department' => 'required|string|max:255',
                'remarks' => 'nullable|string',
                'status' => 'required|in:active,inactive',
            ]);

            if ($validator->fails()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Validation failed.',
                    'errors' => $validator->errors()
                ], 422);
            }

            ServiceRecord::create($request->all());

            Cache::forget('service_record_departments');

            return response()->json([
                'success' => true,
                'message' => 'Service record created successfully.',
                'redirect' => route('admin.service-records.index')
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error creating service record: ' . $e->getMessage()
            ], 500);
        }
    }



    public function show(Request $request, $id)
    {
        try {
            $serviceRecord = ServiceRecord::with('employee')->find($id);

            if (!$serviceRecord) {
                return response()->json([
                    'error' => 'Service record not found'
                ], 404);
            }

            if ($request->ajax()) {
                return view(
                    'admin.service-records.partials.view-modal',
                    compact('serviceRecord')
                );
            }

            return view('admin.service-records.show', compact('serviceRecord'));

        } catch (\Exception $e) {
            return response()->json([
                'error' => 'Error loading service record'
            ], 500);
        }
    }


    public function edit(ServiceRecord $serviceRecord)
    {
        try {
            $employees = Cache::remember('all_employees', 3600, function() {
                return Employee::where('status', 'active')->get();
            });
            return view('admin.service-records.edit', compact('serviceRecord', 'employees'));
        } catch (\Exception $e) {
            return redirect()->route('admin.service-records.index')->with('error', 'Error loading form: ' . $e->getMessage());
        }
    }

    public function update(Request $request, ServiceRecord $serviceRecord)
    {
        try {
            $validator = Validator::make($request->all(), [
                'employee_id' => 'required|exists:employees,id',
                'from_date' => 'required|date',
                'to_date' => 'nullable|date|after_or_equal:from_date',
                'designation' => 'required|string|max:255',
                'department' => 'required|string|max:255',
                'remarks' => 'nullable|string',
                'status' => 'required|in:active,inactive',
            ]);

            if ($validator->fails()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Validation failed.',
                    'errors' => $validator->errors()
                ], 422);
            }

            $serviceRecord->update($request->all());

            Cache::forget('service_record_departments');

            return response()->json([
                'success' => true,
                'message' => 'Service record updated successfully.',
                'redirect' => route('admin.service-records.index')
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error updating service record: ' . $e->getMessage()
            ], 500);
        }
    }

      public function destroy(Request $request, $id)
    {
        try {
            $serviceRecord = ServiceRecord::find($id);

            if (!$serviceRecord) {
                return response()->json([
                    'success' => false,
                    'message' => 'Service record not found.'
                ], 404);
            }

            $serviceRecord->delete();

            Cache::forget('service_record_departments');

            return response()->json([
                'success' => true,
                'message' => 'Service record deleted successfully.'
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error deleting service record.'
            ], 500);
        }
    }

    public function autocompleteDesignation(Request $request)
    {
        try {
            $term = $request->get('term', '');
            $designations = ServiceRecord::where('designation', 'like', "%{$term}%")
                ->distinct()
                ->pluck('designation')
                ->take(10)
                ->toArray();
            return response()->json($designations);
        } catch (\Exception $e) {
            return response()->json([], 500);
        }
    }

    public function autocompleteDepartment(Request $request)
    {
        try {
            $term = $request->get('term', '');
            $departments = ServiceRecord::where('department', 'like', "%{$term}%")
                ->whereNotNull('department')
                ->distinct()
                ->pluck('department')
                ->take(10)
                ->toArray();
            return response()->json($departments);
        } catch (\Exception $e) {
            return response()->json([], 500);
        }
    }
}

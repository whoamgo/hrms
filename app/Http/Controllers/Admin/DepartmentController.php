<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Department;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class DepartmentController extends Controller
{
    public function index()
    {
        try {
            \App\Helpers\ActivityLogHelper::log('viewed', null, 'Department Management');
            return view('admin.departments.index');
        } catch (\Exception $e) {
            return redirect()->route('admin.dashboard')->with('error', 'Error loading departments: ' . $e->getMessage());
        }
    }

    public function getDepartments(Request $request)
    {
        try {
            $query = Department::query();

            if ($request->has('search') && $request->search['value']) {
                $search = $request->search['value'];
                $query->where(function($q) use ($search) {
                    $q->where('name', 'like', "%{$search}%")
                      ->orWhere('description', 'like', "%{$search}%");
                });
            }

            $totalRecords = Department::count();
            $filteredRecords = $query->count();

            $orderColumn = $request->order[0]['column'] ?? 0;
            $orderDir = $request->order[0]['dir'] ?? 'desc';
            $columns = ['id', 'name', 'description', 'is_active', 'created_at'];
            $orderBy = $columns[$orderColumn] ?? 'id';
            $query->orderBy($orderBy, $orderDir);

            $start = $request->start ?? 0;
            $length = $request->length ?? 10;
            $departments = $query->skip($start)->take($length)->get();

            $data = [];
            foreach ($departments as $dept) {
                $data[] = [
                    'id' => $dept->id,
                    'name' => $dept->name,
                    'description' => $dept->description ?? 'N/A',
                    'is_active' => $dept->is_active,
                    'actions' => view('admin.departments.partials.actions', ['department' => $dept])->render(),
                ];
            }

            return response()->json([
                'draw' => intval($request->draw),
                'recordsTotal' => $totalRecords,
                'recordsFiltered' => $filteredRecords,
                'data' => $data
            ]);
        } catch (\Exception $e) {
            return response()->json(['error' => 'Error loading departments: ' . $e->getMessage()], 500);
        }
    }

    public function store(Request $request)
    {
        try {
            $validator = Validator::make($request->all(), [
                'name' => 'required|string|max:255|unique:departments,name',
                'description' => 'nullable|string',
            ]);

            if ($validator->fails()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Validation failed.',
                    'errors' => $validator->errors()
                ], 422);
            }

            $department = Department::create($request->only(['name', 'description']));

            \App\Helpers\ActivityLogHelper::log('created', $department, "Created department: {$department->name}");

            return response()->json([
                'success' => true,
                'message' => 'Department created successfully.',
                'data' => $department
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error creating department: ' . $e->getMessage()
            ], 500);
        }
    }

    public function show(Department $department)
    {
        try {
            \App\Helpers\ActivityLogHelper::log('viewed', $department, "Viewed department: {$department->name}");
            return response()->json([
                'success' => true,
                'data' => $department
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error loading department: ' . $e->getMessage()
            ], 500);
        }
    }

    public function update(Request $request, Department $department)
    {
        try {
            $validator = Validator::make($request->all(), [
                'name' => 'required|string|max:255|unique:departments,name,' . $department->id,
                'description' => 'nullable|string',
            ]);

            if ($validator->fails()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Validation failed.',
                    'errors' => $validator->errors()
                ], 422);
            }

            $oldValues = $department->toArray();
            $department->update($request->only(['name', 'description']));

            \App\Helpers\ActivityLogHelper::log('updated', $department, "Updated department: {$department->name}", $oldValues, $department->toArray());

            return response()->json([
                'success' => true,
                'message' => 'Department updated successfully.',
                'data' => $department
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error updating department: ' . $e->getMessage()
            ], 500);
        }
    }

    public function destroy(Department $department)
    {
        try {
            $deptName = $department->name;
            $department->delete();

            \App\Helpers\ActivityLogHelper::log('deleted', null, "Deleted department: {$deptName}");

            return response()->json([
                'success' => true,
                'message' => 'Department deleted successfully.'
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error deleting department: ' . $e->getMessage()
            ], 500);
        }
    }

    public function toggleStatus(Department $department)
    {
        try {
            $oldStatus = $department->is_active;
            $department->update(['is_active' => !$department->is_active]);

            \App\Helpers\ActivityLogHelper::log('updated', $department, "Toggled department status: {$department->name}");

            return response()->json([
                'success' => true,
                'message' => 'Department status updated successfully.',
                'data' => $department
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error updating status: ' . $e->getMessage()
            ], 500);
        }
    }
}


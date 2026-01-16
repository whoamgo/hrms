<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Designation;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class DesignationController extends Controller
{
    public function index()
    {
        try {
            \App\Helpers\ActivityLogHelper::log('viewed', null, 'Designation Management');
            return view('admin.designations.index');
        } catch (\Exception $e) {
            return redirect()->route('admin.dashboard')->with('error', 'Error loading designations: ' . $e->getMessage());
        }
    }

    public function getDesignations(Request $request)
    {
        try {
            $query = Designation::query();

            if ($request->has('search') && $request->search['value']) {
                $search = $request->search['value'];
                $query->where(function($q) use ($search) {
                    $q->where('name', 'like', "%{$search}%")
                      ->orWhere('description', 'like', "%{$search}%");
                });
            }

            $totalRecords = Designation::count();
            $filteredRecords = $query->count();

            $orderColumn = $request->order[0]['column'] ?? 0;
            $orderDir = $request->order[0]['dir'] ?? 'desc';
            $columns = ['id', 'name', 'description', 'is_active', 'created_at'];
            $orderBy = $columns[$orderColumn] ?? 'id';
            $query->orderBy($orderBy, $orderDir);

            $start = $request->start ?? 0;
            $length = $request->length ?? 10;
            $designations = $query->skip($start)->take($length)->get();

            $data = [];
            foreach ($designations as $desig) {
                $data[] = [
                    'id' => $desig->id,
                    'name' => $desig->name,
                    'description' => $desig->description ?? 'N/A',
                    'is_active' => $desig->is_active,
                    'actions' => view('admin.designations.partials.actions', ['designation' => $desig])->render(),
                ];
            }

            return response()->json([
                'draw' => intval($request->draw),
                'recordsTotal' => $totalRecords,
                'recordsFiltered' => $filteredRecords,
                'data' => $data
            ]);
        } catch (\Exception $e) {
            return response()->json(['error' => 'Error loading designations: ' . $e->getMessage()], 500);
        }
    }

    public function store(Request $request)
    {
        try {
            $validator = Validator::make($request->all(), [
                'name' => 'required|string|max:255|unique:designations,name',
                'description' => 'nullable|string',
            ]);

            if ($validator->fails()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Validation failed.',
                    'errors' => $validator->errors()
                ], 422);
            }

            $designation = Designation::create($request->only(['name', 'description']));

            \App\Helpers\ActivityLogHelper::log('created', $designation, "Created designation: {$designation->name}");

            return response()->json([
                'success' => true,
                'message' => 'Designation created successfully.',
                'data' => $designation
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error creating designation: ' . $e->getMessage()
            ], 500);
        }
    }

    public function show(Designation $designation)
    {
        try {
            \App\Helpers\ActivityLogHelper::log('viewed', $designation, "Viewed designation: {$designation->name}");
            return response()->json([
                'success' => true,
                'data' => $designation
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error loading designation: ' . $e->getMessage()
            ], 500);
        }
    }

    public function update(Request $request, Designation $designation)
    {
        try {
            $validator = Validator::make($request->all(), [
                'name' => 'required|string|max:255|unique:designations,name,' . $designation->id,
                'description' => 'nullable|string',
            ]);

            if ($validator->fails()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Validation failed.',
                    'errors' => $validator->errors()
                ], 422);
            }

            $oldValues = $designation->toArray();
            $designation->update($request->only(['name', 'description']));

            \App\Helpers\ActivityLogHelper::log('updated', $designation, "Updated designation: {$designation->name}", $oldValues, $designation->toArray());

            return response()->json([
                'success' => true,
                'message' => 'Designation updated successfully.',
                'data' => $designation
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error updating designation: ' . $e->getMessage()
            ], 500);
        }
    }

    public function destroy(Designation $designation)
    {
        try {
            $desigName = $designation->name;
            $designation->delete();

            \App\Helpers\ActivityLogHelper::log('deleted', null, "Deleted designation: {$desigName}");

            return response()->json([
                'success' => true,
                'message' => 'Designation deleted successfully.'
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error deleting designation: ' . $e->getMessage()
            ], 500);
        }
    }

    public function toggleStatus(Designation $designation)
    {
        try {
            $oldStatus = $designation->is_active;
            $designation->update(['is_active' => !$designation->is_active]);

            \App\Helpers\ActivityLogHelper::log('updated', $designation, "Toggled designation status: {$designation->name}");

            return response()->json([
                'success' => true,
                'message' => 'Designation status updated successfully.',
                'data' => $designation
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error updating status: ' . $e->getMessage()
            ], 500);
        }
    }
}


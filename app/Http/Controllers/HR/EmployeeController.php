<?php

namespace App\Http\Controllers\HR;

use App\Http\Controllers\Controller;
use App\Models\Employee;
use App\Models\User;
use App\Models\Role;
use App\Models\Department;
use App\Models\Designation;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\Rule;

class EmployeeController extends Controller
{
    public function index(Request $request)
    {
        try {
            $departments = Cache::remember('employee_departments', 3600, function() {
                return Employee::whereNotNull('department')
                    ->distinct()
                    ->pluck('department')
                    ->sort()
                    ->values();
            });

            $employeeTypes = ['Permanent', 'Contract'];
            $roles = Cache::remember('all_roles', 3600, function() {
                return Role::where('is_active', true)->get();
            });

            \App\Helpers\ActivityLogHelper::log('viewed', null, 'HR - Employee Master');
            return view('hr.employees.index', compact('departments', 'employeeTypes', 'roles'));
        } catch (\Exception $e) {
            return redirect()->back()->with('error', 'Error loading employees: ' . $e->getMessage());
        }
    }

    public function getEmployees(Request $request)
    {
        try {
            $query = Employee::with('user');

            if ($request->has('search') && $request->search['value']) {
                $search = $request->search['value'];
                $query->where(function($q) use ($search) {
                    $q->where('employee_id', 'like', "%{$search}%")
                      ->orWhere('full_name', 'like', "%{$search}%")
                      ->orWhere('email', 'like', "%{$search}%")
                      ->orWhere('department', 'like', "%{$search}%");
                });
            }

            if ($request->filled('employee_id')) {
                $query->where('employee_id', $request->employee_id);
            }

            if ($request->filled('name')) {
                $query->where('full_name', 'like', "%{$request->name}%");
            }

            if ($request->filled('employee_type')) {
                $query->where('employee_type', $request->employee_type);
            }

            if ($request->filled('department')) {
                $query->where('department', $request->department);
            }

            if ($request->filled('role_id')) {
                $query->whereHas('user', function($q) use ($request) {
                    $q->where('role_id', $request->role_id);
                });
            }

            if ($request->filled('status')) {
                $query->where('status', $request->status);
            }

            $totalRecords = Employee::count();
            $filteredRecords = $query->count();

            $orderColumn = $request->order[0]['column'] ?? 0;
            $orderDir = $request->order[0]['dir'] ?? 'asc';
            $columns = ['id', 'employee_id', 'full_name', 'employee_type', 'department', 'status', 'created_at'];
            $orderBy = $columns[$orderColumn] ?? 'id';
            $query->orderBy($orderBy, $orderDir);

            $start = $request->start ?? 0;
            $length = $request->length ?? 10;
            $employees = $query->skip($start)->take($length)->get();

            $data = [];
            foreach ($employees as $employee) {
                $data[] = [
                    'id' => $employee->id,
                    'route_key' => $employee->getRouteKey(),
                    'employee_id' => $employee->employee_id,
                    'name' => $employee->full_name,
                    'employee_type' => $employee->employee_type,
                    'department' => $employee->department ?? 'N/A',
                    'status' => $employee->status,
                    'created_at' => $employee->created_at->format('Y-m-d H:i:s'),
                    'actions' => view('hr.employees.partials.actions', ['employee' => $employee])->render(),
                ];
            }

            return response()->json([
                'draw' => intval($request->draw),
                'recordsTotal' => $totalRecords,
                'recordsFiltered' => $filteredRecords,
                'data' => $data
            ]);
        } catch (\Exception $e) {
            return response()->json(['error' => 'Error loading employees: ' . $e->getMessage()], 500);
        }
    }

    public function create()
    {
        try {
            $departments = Cache::remember('all_departments', 3600, function() {
                return Department::where('is_active', true)->orderBy('name')->get();
            });

            $designations = Cache::remember('all_designations', 3600, function() {
                return Designation::where('is_active', true)->orderBy('name')->get();
            });

            return view('hr.employees.create', compact('departments', 'designations'));
        } catch (\Exception $e) {
            return redirect()->route('hr.employees.index')->with('error', 'Error loading form: ' . $e->getMessage());
        }
    }

    public function store(Request $request)
    {
        try {
            $validator = Validator::make($request->all(), [
                'employee_type' => 'required|in:Permanent,Contract',
                'full_name' => 'required|string|max:255',
                'father_mother_name' => 'nullable|string|max:255',
                'dob' => 'nullable|date',
                'gender' => 'nullable|in:Male,Female,Transgender,Other',
                'mobile_number' => 'required|string|max:20',
                'employee_email' => 'required|email|max:255|unique:users,email',
                'password' => 'required|string|min:8',
                'address' => 'nullable|string',
                'bank_account_number' => 'nullable|string|max:255',
                'bank_name' => 'nullable|string|max:255',
                'ifsc_code' => 'nullable|string|max:255',
                'pan_card_number' => 'nullable|string|max:255',
                'department_id' => 'nullable|exists:departments,id',
                'designation_id' => 'nullable|exists:designations,id',
                'date_of_joining' => 'nullable|date',
                'employment_status' => 'nullable|string|max:255',
                'contract_start_date' => 'nullable|date|required_if:employee_type,Contract',
                'contract_end_date' => 'nullable|date|after_or_equal:contract_start_date',
                'appointment_letter' => 'nullable|file|mimes:pdf,doc,docx|max:5120',
                'id_proof' => 'nullable|file|mimes:pdf,doc,docx,jpg,jpeg,png|max:5120',
                'status' => 'required|in:active,inactive',
            ], [
                'employee_type.required' => 'Employee type is required.',
                'full_name.required' => 'Full name is required.',
                'mobile_number.required' => 'Mobile number is required.',
                'employee_email.required' => 'Email is required.',
                'employee_email.unique' => 'This email is already registered.',
                'password.required' => 'Password is required.',
                'password.min' => 'Password must be at least 8 characters.',
                'contract_start_date.required_if' => 'Contract start date is required for contract employees.',
                'contract_end_date.after_or_equal' => 'Contract end date must be after or equal to start date.',
                'appointment_letter.max' => 'Appointment letter size must not exceed 5MB.',
                'id_proof.max' => 'ID proof size must not exceed 5MB.',
            ]);

            if ($validator->fails()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Validation failed.',
                    'errors' => $validator->errors()
                ], 422);
            }

            // Generate username from email (sanitize special characters)
            $email = $request->employee_email;
            $username = preg_replace('/[^a-z0-9]/', '', strtolower(explode('@', $email)[0]));
            
            // Ensure username is unique
            $originalUsername = $username;
            $counter = 1;
            while (User::where('username', $username)->exists()) {
                $username = $originalUsername . $counter;
                $counter++;
            }

            // Create user account automatically with role_id 4 (Employee) and status active
            // Use database transaction to ensure data consistency
            DB::beginTransaction();
            try {
                $user = User::create([
                    'name' => $request->full_name,
                    'username' => $username,
                    'email' => $email,
                    'password' => Hash::make($request->password),
                    'role_id' => 4, // Default Employee role
                    'status' => 'active', // Default active status
                ]);
                $userId = $user->id;
                \App\Helpers\ActivityLogHelper::log('created', $user, "HR - Created user for employee: {$request->full_name}");

                // Generate employee ID
                $employeeId = Employee::generateEmployeeId();

                // Handle file uploads
                $appointmentLetterPath = null;
                $idProofPath = null;

                if ($request->hasFile('appointment_letter')) {
                    $appointmentLetterPath = $request->file('appointment_letter')->store('documents/appointment-letters', 'public');
                }

                if ($request->hasFile('id_proof')) {
                    $idProofPath = $request->file('id_proof')->store('documents/id-proofs', 'public');
                }

                // Get department and designation names
                $departmentName = null;
                $designationName = null;
                if ($request->department_id) {
                    $dept = Department::find($request->department_id);
                    $departmentName = $dept ? $dept->name : null;
                }
                if ($request->designation_id) {
                    $desig = Designation::find($request->designation_id);
                    $designationName = $desig ? $desig->name : null;
                }

                $employee = Employee::create([
                    'employee_id' => $employeeId,
                    'user_id' => $userId, // Link to automatically created user
                    'employee_type' => $request->employee_type,
                    'full_name' => $request->full_name,
                    'father_mother_name' => $request->father_mother_name,
                    'dob' => $request->dob,
                    'gender' => $request->gender,
                    'mobile_number' => $request->mobile_number,
                    'email' => $email,
                    'address' => $request->address,
                    'bank_account_number' => $request->bank_account_number,
                    'bank_branch_name' => $request->bank_branch_name,
                    'account_holder_name' => $request->account_holder_name,
                    'bank_account_number' => $request->bank_account_number,
                    'bank_name' => $request->bank_name,
                    'ifsc_code' => $request->ifsc_code,
                    'pan_card_number' => $request->pan_card_number,
                    'department' => $departmentName,
                    'department_id' => $request->department_id,
                    'designation' => $designationName,
                    'designation_id' => $request->designation_id,
                    'date_of_joining' => $request->date_of_joining,
                    //'employment_status' => $request->employment_status,
                    'contract_start_date' => $request->contract_start_date,
                    'contract_end_date' => $request->contract_end_date,
                    'appointment_letter' => $appointmentLetterPath,
                    'id_proof' => $idProofPath,
                    'status' => $request->status,
                ]);

                \App\Helpers\ActivityLogHelper::log('created', $employee, "HR - Created employee: {$employee->full_name}");

                // Commit transaction
                DB::commit();

                // Clear cache
                Cache::forget('employee_departments');
                Cache::forget('user_menu_' . $userId);

                return response()->json([
                    'success' => true,
                    'message' => 'Employee created successfully.',
                    'redirect' => route('hr.employees.index')
                ]);
            } catch (\Exception $e) {
                DB::rollBack();
                throw $e;
            }

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error creating employee: ' . $e->getMessage()
            ], 500);
        }
    }

    public function show(Employee $employee, Request $request)
    {
        try {
            $employee->load('user');
            if ($request->ajax()) {
                return view('hr.employees.partials.view-modal', compact('employee'))->render();
            }
            return view('hr.employees.show', compact('employee'));
        } catch (\Exception $e) {
            if ($request->ajax()) {
                return response()->json(['error' => 'Error loading employee: ' . $e->getMessage()], 500);
            }
            return redirect()->route('hr.employees.index')->with('error', 'Error loading employee: ' . $e->getMessage());
        }
    }

    public function edit(Employee $employee)
    {
        try {
            $employee->load('user'); // Load user relationship
            
            $departments = Cache::remember('all_departments', 3600, function() {
                return Department::where('is_active', true)->orderBy('name')->get();
            });

            $designations = Cache::remember('all_designations', 3600, function() {
                return Designation::where('is_active', true)->orderBy('name')->get();
            });

            return view('hr.employees.edit', compact('employee', 'departments', 'designations'));
        } catch (\Exception $e) {
            return redirect()->route('hr.employees.index')->with('error', 'Error loading form: ' . $e->getMessage());
        }
    }

    public function update(Request $request, Employee $employee)
    {
        try {
            DB::beginTransaction();
            
            $validator = Validator::make($request->all(), [
                'employee_type' => 'required|in:Permanent,Contract',
                'full_name' => 'required|string|max:255',
                'father_mother_name' => 'nullable|string|max:255',
                'dob' => 'nullable|date',
                'gender' => 'nullable|in:Male,Female,Transgender,Other',
                'mobile_number' => 'required|string|max:20',
                'employee_email' => [
                    'required',
                    'email',
                    'max:255',
                    Rule::unique('users', 'email')->ignore($employee->user_id ?? 0)
                ],
                'password' => 'nullable|string|min:8',
                'address' => 'nullable|string',
                'bank_account_number' => 'nullable|string|max:255',
                'bank_name' => 'nullable|string|max:255',
                'ifsc_code' => 'nullable|string|max:255',
                'pan_card_number' => 'nullable|string|max:255',
                'department_id' => 'nullable|exists:departments,id',
                'designation_id' => 'nullable|exists:designations,id',
                'date_of_joining' => 'nullable|date',
                'employment_status' => 'nullable|string|max:255',
                'contract_start_date' => 'nullable|date|required_if:employee_type,Contract',
                'contract_end_date' => 'nullable|date|after_or_equal:contract_start_date',
                'appointment_letter' => 'nullable|file|mimes:pdf,doc,docx|max:5120',
                'id_proof' => 'nullable|file|mimes:pdf,doc,docx,jpg,jpeg,png|max:5120',
                'status' => 'required|in:active,inactive',
            ], [
                'employee_type.required' => 'Employee type is required.',
                'full_name.required' => 'Full name is required.',
                'mobile_number.required' => 'Mobile number is required.',
                'employee_email.required' => 'Email is required.',
                'employee_email.unique' => 'This email is already registered.',
                'password.min' => 'Password must be at least 8 characters.',
                'contract_start_date.required_if' => 'Contract start date is required for contract employees.',
                'contract_end_date.after_or_equal' => 'Contract end date must be after or equal to start date.',
                'appointment_letter.max' => 'Appointment letter size must not exceed 5MB.',
                'id_proof.max' => 'ID proof size must not exceed 5MB.',
            ]);

            if ($validator->fails()) {
                DB::rollBack();
                return response()->json([
                    'success' => false,
                    'message' => 'Validation failed.',
                    'errors' => $validator->errors()
                ], 422);
            }

            // Update user account if exists
            if ($employee->user_id) {
                $user = User::find($employee->user_id);
                if ($user) {
                    $userUpdateData = [
                        'name' => $request->full_name,
                        'email' => $request->employee_email,
                    ];
                    
                    // Update password if provided
                    if ($request->filled('password')) {
                        $userUpdateData['password'] = Hash::make($request->password);
                    }
                    
                    $user->update($userUpdateData);
                }
            }

            // Get department and designation names
            $departmentName = null;
            $designationName = null;
            if ($request->department_id) {
                $dept = Department::find($request->department_id);
                $departmentName = $dept ? $dept->name : null;
            }
            if ($request->designation_id) {
                $desig = Designation::find($request->designation_id);
                $designationName = $desig ? $desig->name : null;
            }

            $oldValues = $employee->toArray();
            $updateData = [
                'employee_type' => $request->employee_type,
                'full_name' => $request->full_name,
                'father_mother_name' => $request->father_mother_name,
                'dob' => $request->dob,
                'gender' => $request->gender,
                'mobile_number' => $request->mobile_number,
                'email' => $request->employee_email,
                'address' => $request->address,
                'account_holder_name' => $request->account_holder_name,
                'bank_branch_name' => $request->bank_branch_name,
                'bank_account_number' => $request->bank_account_number,
                'bank_name' => $request->bank_name,
                'ifsc_code' => $request->ifsc_code,
                'pan_card_number' => $request->pan_card_number,
                'department' => $departmentName,
                'department_id' => $request->department_id,
                'designation' => $designationName,
                'designation_id' => $request->designation_id,
                'date_of_joining' => $request->date_of_joining,
               // 'employment_status' => $request->employment_status,
                'contract_start_date' => $request->contract_start_date,
                'contract_end_date' => $request->contract_end_date,
                'status' => $request->status,
            ];

            // Handle file uploads
            if ($request->hasFile('appointment_letter')) {
                if ($employee->appointment_letter && Storage::disk('public')->exists($employee->appointment_letter)) {
                    Storage::disk('public')->delete($employee->appointment_letter);
                }
                $updateData['appointment_letter'] = $request->file('appointment_letter')->store('documents/appointment-letters', 'public');
            }

            if ($request->hasFile('id_proof')) {
                if ($employee->id_proof && Storage::disk('public')->exists($employee->id_proof)) {
                    Storage::disk('public')->delete($employee->id_proof);
                }
                $updateData['id_proof'] = $request->file('id_proof')->store('documents/id-proofs', 'public');
            }

            $employee->update($updateData);

            DB::commit();

            \App\Helpers\ActivityLogHelper::log('updated', $employee, "HR - Updated employee: {$employee->full_name}", $oldValues, $employee->toArray());

            Cache::forget('employee_departments');
            if ($employee->user_id) {
                Cache::forget('user_menu_' . $employee->user_id);
            }

            return response()->json([
                'success' => true,
                'message' => 'Employee updated successfully.',
                'redirect' => route('hr.employees.index')
            ]);
        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json([
                'success' => false,
                'message' => 'Error updating employee: ' . $e->getMessage()
            ], 500);
        }
    }

    public function destroy(Employee $employee)
    {
        try {
            $employeeName = $employee->full_name;
            $userId = $employee->user_id;

            // Delete files
            if ($employee->appointment_letter && Storage::disk('public')->exists($employee->appointment_letter)) {
                Storage::disk('public')->delete($employee->appointment_letter);
            }

            if ($employee->id_proof && Storage::disk('public')->exists($employee->id_proof)) {
                Storage::disk('public')->delete($employee->id_proof);
            }

            $employee->delete();

            // Delete user if exists
            if ($userId) {
                $user = User::find($userId);
                if ($user) {
                    $user->delete();
                    \App\Helpers\ActivityLogHelper::log('deleted', null, "HR - Deleted user for employee: {$employeeName}");
                }
            }

            \App\Helpers\ActivityLogHelper::log('deleted', null, "HR - Deleted employee: {$employeeName}");

            Cache::forget('employee_departments');
            Cache::forget('users_without_employee');

            return response()->json([
                'success' => true,
                'message' => 'Employee deleted successfully.'
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error deleting employee: ' . $e->getMessage()
            ], 500);
        }
    }

    public function toggleStatus(Employee $employee)
    {


//echo "okfine"; die();
        try {
            $employee->status = $employee->status === 'active' ? 'inactive' : 'active';
            $employee->save();

            return response()->json([
                'success' => true,
                'message' => 'Employee status updated successfully.',
                'status' => $employee->status
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error updating status: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Get autocomplete suggestions for Employee ID
     */
    public function autocompleteEmployeeId(Request $request)
    {
        try {
            $term = $request->get('term', '');
            
            $employeeIds = Employee::where('employee_id', 'like', "%{$term}%")
                ->distinct()
                ->pluck('employee_id')
                ->take(10)
                ->toArray();

            return response()->json($employeeIds);
        } catch (\Exception $e) {
            return response()->json([], 500);
        }
    }

    /**
     * Get autocomplete suggestions for Employee Name
     */
    public function autocompleteEmployeeName(Request $request)
    {
        try {
            $term = $request->get('term', '');
            
            $names = Employee::where('full_name', 'like', "%{$term}%")
                ->distinct()
                ->pluck('full_name')
                ->take(10)
                ->toArray();

            return response()->json($names);
        } catch (\Exception $e) {
            return response()->json([], 500);
        }
    }

    /**
     * Get autocomplete suggestions for Department
     */
    public function autocompleteDepartment(Request $request)
    {
        try {
            $term = $request->get('term', '');
            
            $departments = Employee::where('department', 'like', "%{$term}%")
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

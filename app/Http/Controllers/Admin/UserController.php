<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Models\Role;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Storage;

class UserController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        try {
            $roles = Cache::remember('all_roles', 3600, function() {
                return Role::where('is_active', true)->where('slug','!=','employee')->get();
            });

            return view('admin.users.index', compact('roles'));
        } catch (\Exception $e) {
            return redirect()->back()->with('error', 'Error loading users: ' . $e->getMessage());
        }
    }

    /**
     * Get users data for DataTable
     */
    public function getUsers(Request $request)
    {
        try {
            $query = User::with('role')->where('role_id','!=',4);

            // Apply filters
            if ($request->has('search') && $request->search['value']) {
                $search = $request->search['value'];
                $query->where(function($q) use ($search) {
                    $q->where('name', 'like', "%{$search}%")
                      ->orWhere('username', 'like', "%{$search}%")
                      ->orWhere('email', 'like', "%{$search}%");
                });
            }
            
            //  echo $request->has('role_id').'==='.$request->has('status'); die();

            // if ($request->has('role_id') && $request->role_id) {
            //     $query->where('role_id', $request->role_id);
            // }

            // if ($request->status !== '' && $request->status !=1) {
            //     $query->where('status', $request->status);
            // }

            // Get total count before pagination
            $totalRecords = User::count();
            $filteredRecords = $query->count();

            // Apply ordering
            $orderColumn = $request->order[0]['column'] ?? 0;
            $orderDir = $request->order[0]['dir'] ?? 'asc';
            $columns = ['id', 'name', 'username', 'email', 'role_id', 'status', 'created_at'];
            $orderBy = $columns[$orderColumn] ?? 'id';
            $query->orderBy($orderBy, $orderDir);

            // Apply pagination
            $start = $request->start ?? 0;
            $length = $request->length ?? 10;
            $users = $query->skip($start)->take($length)->get();

            $data = [];
            foreach ($users as $user) {
                $data[] = [
                    'id' => $user->id,
                    'name' => $user->name,
                   // 'image' => $user->name,
                    'username' => $user->username,
                    'email' => $user->email,
                    'role' => $user->role ? $user->role->name : 'No Role',
                    'status' => $user->status,
                    'created_at' => $user->created_at->format('Y-m-d H:i:s'),
                    'actions' => view('admin.users.partials.actions', compact('user'))->render(),
                ];
            }

            return response()->json([
                'draw' => intval($request->draw),
                'recordsTotal' => $totalRecords,
                'recordsFiltered' => $filteredRecords,
                'data' => $data
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'error' => 'Error loading users: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        try {
            $roles = Cache::remember('all_roles', 3600, function() {
                return Role::where('is_active', true)->get();
            });

            return view('admin.users.create', compact('roles'));
        } catch (\Exception $e) {
            return redirect()->route('admin.users.index')->with('error', 'Error loading form: ' . $e->getMessage());
        }
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        try {
            $validator = Validator::make($request->all(), [
                'name' => 'required|string|max:255',
                'username' => 'required|string|max:255|unique:users,username',
                'email' => 'required|email|max:255|unique:users,email',
                'password' => 'required|string|min:6|confirmed',
                'role_id' => 'required|exists:roles,id',
                'status' => 'required|in:active,inactive',
                'avatar' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
            ], [
                'name.required' => 'Name is required.',
                'username.required' => 'Username is required.',
                'username.unique' => 'This username is already taken.',
                'email.required' => 'Email is required.',
                'email.email' => 'Please enter a valid email address.',
                'email.unique' => 'This email is already registered.',
                'password.required' => 'Password is required.',
                'password.min' => 'Password must be at least 6 characters.',
                'password.confirmed' => 'Password confirmation does not match.',
                'role_id.required' => 'Please select a role.',
                'role_id.exists' => 'Selected role is invalid.',
                'status.required' => 'Please select a status.',
                'avatar.image' => 'Avatar must be an image.',
                'avatar.max' => 'Avatar size must not exceed 2MB.',
            ]);

            if ($validator->fails()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Validation failed.',
                    'errors' => $validator->errors()
                ], 422);
            }

            $avatarPath = null;
            if ($request->hasFile('avatar')) {
                $avatarPath = $request->file('avatar')->store('avatars', 'public');
            }

            $user = User::create([
                'name' => $request->name,
                'username' => $request->username,
                'email' => $request->email,
                'password' => Hash::make($request->password),
                'role_id' => $request->role_id,
                'status' => $request->status,
                'avatar' => $avatarPath,
            ]);

            // Clear cache
            Cache::forget('all_roles');
            Cache::forget('user_menu_' . $user->id);

            return response()->json([
                'success' => true,
                'message' => 'User created successfully.',
                'redirect' => route('admin.users.index')
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error creating user: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Display the specified resource.
     */
    public function show(User $user, Request $request)
    {
        try {
            $user->load('role');
            
            // If AJAX request, return modal content
            if ($request->ajax()) {
                return view('admin.users.partials.view-modal', compact('user'))->render();
            }
            
            return view('admin.users.show', compact('user'));
        } catch (\Exception $e) {
            if ($request->ajax()) {
                return response()->json([
                    'error' => 'Error loading user: ' . $e->getMessage()
                ], 500);
            }
            return redirect()->route('admin.users.index')->with('error', 'Error loading user: ' . $e->getMessage());
        }
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(User $user)
    {
        try {
            $roles = Cache::remember('all_roles', 3600, function() {
                return Role::where('is_active', true)->where('slug','!=','employee')->get();
            });
           // echo "<pre>"; print_r($roles); die();
            return view('admin.users.edit', compact('user', 'roles'));
        } catch (\Exception $e) {
            return redirect()->route('admin.users.index')->with('error', 'Error loading form: ' . $e->getMessage());
        }
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, User $user)
    {
       // echo $user->id; die();
        try {
            $validator = Validator::make($request->all(), [
                'name' => 'required|string|max:255',
                'username' => 'required|string|max:255|unique:users,username,' . $user->id,
                'email' => 'required|email|max:255|unique:users,email,' . $user->id,
                'password' => 'nullable|string|min:6|confirmed',
                'role_id' => 'required|exists:roles,id',
                'status' => 'required|in:active,inactive',
                'avatar' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
            ], [
                'name.required' => 'Name is required.',
                'username.required' => 'Username is required.',
                'username.unique' => 'This username is already taken.',
                'email.required' => 'Email is required.',
                'email.email' => 'Please enter a valid email address.',
                'email.unique' => 'This email is already registered.',
                'password.min' => 'Password must be at least 6 characters.',
                'password.confirmed' => 'Password confirmation does not match.',
                'role_id.required' => 'Please select a role.',
                'role_id.exists' => 'Selected role is invalid.',
                'status.required' => 'Please select a status.',
                'avatar.image' => 'Avatar must be an image.',
                'avatar.max' => 'Avatar size must not exceed 2MB.',
            ]);

            if ($validator->fails()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Validation failed.',
                    'errors' => $validator->errors()
                ], 422);
            }

            $updateData = [
                'name' => $request->name,
                'username' => $request->username,
                'email' => $request->email,
                'role_id' => $request->role_id,
                'status' => $request->status,
            ];

            if ($request->filled('password')) {
                $updateData['password'] = Hash::make($request->password);
            }

            if ($request->hasFile('avatar')) {
                // Delete old avatar
                if ($user->avatar && Storage::disk('public')->exists($user->avatar)) {
                    Storage::disk('public')->delete($user->avatar);
                }
                $updateData['avatar'] = $request->file('avatar')->store('avatars', 'public');
            }

            $user->update($updateData);

            // Clear cache
            Cache::forget('all_roles');
            Cache::forget('user_menu_' . $user->id);

            return response()->json([
                'success' => true,
                'message' => 'User updated successfully.',
                'redirect' => route('admin.users.index')
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error updating user: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(User $user)
    {
        try {
            // Prevent deleting own account
            if ($user->id === auth()->id()) {
                return response()->json([
                    'success' => false,
                    'message' => 'You cannot delete your own account.'
                ], 403);
            }

            // Delete avatar if exists
            if ($user->avatar && Storage::disk('public')->exists($user->avatar)) {
                Storage::disk('public')->delete($user->avatar);
            }

            $userId = $user->id;
            $user->delete();

            // Clear cache
            Cache::forget('user_menu_' . $userId);
            Cache::forget('all_roles');

            return response()->json([
                'success' => true,
                'message' => 'User deleted successfully.'
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error deleting user: ' . $e->getMessage()
            ], 500);
        }
    }
}

<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Role;
use App\Models\Permission;
use App\Models\MenuItem;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Validator;

class RoleController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        try {
            $roles = Role::with('permissions', 'menuItems')->get();
            return view('admin.roles.index', compact('roles'));
        } catch (\Exception $e) {
            return redirect()->back()->with('error', 'Error loading roles: ' . $e->getMessage());
        }
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        return redirect('/admin/roles');
        $permissions = Permission::where('is_active', true)->get()->groupBy('module');
        $menuItems = MenuItem::where('is_active', true)->whereNull('parent_id')->orderBy('order')->get();
        return view('admin.roles.create', compact('permissions', 'menuItems'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        try {
            $validator = Validator::make($request->all(), [
                'name' => 'required|string|max:255',
                'slug' => [
                    'required',
                    'string',
                    'max:255',
                    'unique:roles,slug',
                    'regex:/^[a-z0-9]+(?:-[a-z0-9]+)*$/',
                ],
                'description' => 'nullable|string|max:1000',
                'permissions' => 'nullable|array',
                'permissions.*' => 'exists:permissions,id',
                'menu_items' => 'nullable|array',
                'menu_items.*' => 'exists:menu_items,id',
            ], [
                'name.required' => 'Role name is required.',
                'slug.required' => 'Slug is required.',
                'slug.unique' => 'This slug is already taken.',
                'slug.regex' => 'Slug can only contain lowercase letters, numbers, and hyphens. It cannot start or end with a hyphen.',
                'permissions.*.exists' => 'One or more selected permissions are invalid.',
                'menu_items.*.exists' => 'One or more selected menu items are invalid.',
            ]);

            if ($validator->fails()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Validation failed.',
                    'errors' => $validator->errors()
                ], 422);
            }

            $role = Role::create([
                'name' => $request->name,
                'slug' => $request->slug,
                'description' => $request->description,
                'is_active' => $request->has('is_active'),
            ]);

            if ($request->has('permissions')) {
                $role->permissions()->sync($request->permissions);
            }

            if ($request->has('menu_items')) {
                $role->menuItems()->sync($request->menu_items);
            }

            // Clear cache
            Cache::flush();

            return response()->json([
                'success' => true,
                'message' => 'Role created successfully.',
                'redirect' => route('admin.roles.index')
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error creating role: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Display the specified resource.
     */
    public function show(Role $role)
    {
        $role->load('permissions', 'menuItems', 'users');
        return view('admin.roles.show', compact('role'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Role $role)
    {
        return redirect('/admin/roles');
        $permissions = Permission::where('is_active', true)->get()->groupBy('module');
        $menuItems = MenuItem::where('is_active', true)->whereNull('parent_id')->orderBy('order')->get();
        $role->load('permissions', 'menuItems');
        return view('admin.roles.edit', compact('role', 'permissions', 'menuItems'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Role $role)
    {
        try {
            $validator = Validator::make($request->all(), [
                'name' => 'required|string|max:255',
                'slug' => [
                    'required',
                    'string',
                    'max:255',
                    'unique:roles,slug,' . $role->id,
                    'regex:/^[a-z0-9]+(?:-[a-z0-9]+)*$/',
                ],
                'description' => 'nullable|string|max:1000',
                'permissions' => 'nullable|array',
                'permissions.*' => 'exists:permissions,id',
                'menu_items' => 'nullable|array',
                'menu_items.*' => 'exists:menu_items,id',
            ], [
                'name.required' => 'Role name is required.',
                'slug.required' => 'Slug is required.',
                'slug.unique' => 'This slug is already taken.',
                'slug.regex' => 'Slug can only contain lowercase letters, numbers, and hyphens. It cannot start or end with a hyphen.',
                'permissions.*.exists' => 'One or more selected permissions are invalid.',
                'menu_items.*.exists' => 'One or more selected menu items are invalid.',
            ]);

            if ($validator->fails()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Validation failed.',
                    'errors' => $validator->errors()
                ], 422);
            }

            $role->update([
                'name' => $request->name,
                'slug' => $request->slug,
                'description' => $request->description,
                'is_active' => $request->has('is_active'),
            ]);

            if ($request->has('permissions')) {
                $role->permissions()->sync($request->permissions);
            } else {
                $role->permissions()->detach();
            }

            if ($request->has('menu_items')) {
                $role->menuItems()->sync($request->menu_items);
            } else {
                $role->menuItems()->detach();
            }

            // Clear cache for all users with this role
            foreach ($role->users as $user) {
                Cache::forget('user_menu_' . $user->id);
            }
            Cache::flush();

            return response()->json([
                'success' => true,
                'message' => 'Role updated successfully.',
                'redirect' => route('admin.roles.index')
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error updating role: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Role $role)
    {
        try {
            // Clear cache for all users with this role
            foreach ($role->users as $user) {
                Cache::forget('user_menu_' . $user->id);
            }

            $role->delete();
            Cache::flush();

            return response()->json([
                'success' => true,
                'message' => 'Role deleted successfully.'
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error deleting role: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Update permissions for a role
     */
    public function updatePermissions(Request $request, Role $role)
    {
        try {
            $validator = Validator::make($request->all(), [
                'permissions' => 'required|array',
            ]);

            if ($validator->fails()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Validation failed.',
                    'errors' => $validator->errors()
                ], 422);
            }

            $role->permissions()->sync($request->permissions);

            // Clear cache
            Cache::flush();

            return response()->json([
                'success' => true,
                'message' => 'Permissions updated successfully.'
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error updating permissions: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Update menu items for a role
     */
    public function updateMenuItems(Request $request, Role $role)
    {
        try {
            $validator = Validator::make($request->all(), [
                'menu_items' => 'required|array',
            ]);

            if ($validator->fails()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Validation failed.',
                    'errors' => $validator->errors()
                ], 422);
            }

            $role->menuItems()->sync($request->menu_items);

            // Clear cache for all users with this role
            foreach ($role->users as $user) {
                Cache::forget('user_menu_' . $user->id);
            }

            return response()->json([
                'success' => true,
                'message' => 'Menu items updated successfully.'
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error updating menu items: ' . $e->getMessage()
            ], 500);
        }
    }
}

<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;

class RolePermissionController extends Controller
{
    public function __construct()
    {
        $this->middleware('permission:view roles')->only(['rolesIndex', 'rolesShow']);
        $this->middleware('permission:create roles')->only(['rolesCreate', 'rolesStore']);
        $this->middleware('permission:edit roles')->only(['rolesEdit', 'rolesUpdate']);
        $this->middleware('permission:delete roles')->only(['rolesDestroy']);

        $this->middleware('permission:view permissions')->only(['permissionsIndex']);
        $this->middleware('permission:create permissions')->only(['permissionsCreate', 'permissionsStore']);
        $this->middleware('permission:edit permissions')->only(['permissionsEdit', 'permissionsUpdate']);
        $this->middleware('permission:delete permissions')->only(['permissionsDestroy']);
    }

    /**
     * Display a listing of roles.
     */
    public function rolesIndex(Request $request)
    {
        $query = Role::withCount('users');

        // Search
        if ($request->filled('search')) {
            $query->where('name', 'like', '%' . $request->search . '%');
        }

        // Sorting
        $sortBy = $request->get('sort_by', 'name');
        $sortOrder = $request->get('sort_order', 'asc');

        $allowedSortColumns = ['name', 'created_at'];
        if (!in_array($sortBy, $allowedSortColumns)) {
            $sortBy = 'name';
        }

        if (!in_array($sortOrder, ['asc', 'desc'])) {
            $sortOrder = 'asc';
        }

        $query->orderBy($sortBy, $sortOrder);

        $roles = $query->paginate(10)->withQueryString();

        return view('admin.pages.roles.index', compact('roles'));
    }

    /**
     * Show the form for creating a new role.
     */
    public function rolesCreate()
    {
        $permissions = Permission::orderBy('name')->get()->groupBy(function ($permission) {
            $parts = explode(' ', $permission->name);
            return count($parts) > 1 ? $parts[0] : 'other';
        });

        return view('admin.pages.roles.create', compact('permissions'));
    }

    /**
     * Store a newly created role.
     */
    public function rolesStore(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255|unique:roles,name',
            'permissions' => 'nullable|array',
            'permissions.*' => 'exists:permissions,id',
        ]);

        $role = Role::create(['name' => $validated['name']]);

        if ($request->filled('permissions')) {
            $role->syncPermissions($request->permissions);
        }

        return redirect()->route('roles.index')
            ->with('success', __('Role created successfully'));
    }

    /**
     * Display the specified role.
     */
    public function rolesShow(Role $role)
    {
        $role->load('permissions', 'users');
        $permissions = Permission::orderBy('name')->get()->groupBy(function ($permission) {
            $parts = explode(' ', $permission->name);
            return count($parts) > 1 ? $parts[0] : 'other';
        });

        return view('admin.pages.roles.show', compact('role', 'permissions'));
    }

    /**
     * Show the form for editing the specified role.
     */
    public function rolesEdit(Role $role)
    {
        $permissions = Permission::orderBy('name')->get()->groupBy(function ($permission) {
            $parts = explode(' ', $permission->name);
            return count($parts) > 1 ? $parts[0] : 'other';
        });

        $role->load('permissions');

        return view('admin.pages.roles.edit', compact('role', 'permissions'));
    }

    /**
     * Update the specified role.
     */
    public function rolesUpdate(Request $request, Role $role)
    {
        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255', Rule::unique('roles')->ignore($role->id)],
            'permissions' => 'nullable|array',
            'permissions.*' => 'exists:permissions,id',
        ]);

        $role->update(['name' => $validated['name']]);

        if ($request->has('permissions')) {
            $role->syncPermissions($request->permissions ?? []);
        }

        return redirect()->route('roles.index')
            ->with('success', __('Role updated successfully'));
    }

    /**
     * Remove the specified role.
     */
    public function rolesDestroy(Role $role)
    {
        $role->delete();

        return redirect()->route('roles.index')
            ->with('success', __('Role deleted successfully'));
    }

    /**
     * Display a listing of permissions.
     */
    public function permissionsIndex(Request $request)
    {
        $query = Permission::query();

        // Search
        if ($request->filled('search')) {
            $query->where('name', 'like', '%' . $request->search . '%');
        }

        // Sorting
        $sortBy = $request->get('sort_by', 'name');
        $sortOrder = $request->get('sort_order', 'asc');

        $allowedSortColumns = ['name', 'created_at'];
        if (!in_array($sortBy, $allowedSortColumns)) {
            $sortBy = 'name';
        }

        if (!in_array($sortOrder, ['asc', 'desc'])) {
            $sortOrder = 'asc';
        }

        $query->orderBy($sortBy, $sortOrder);

        $permissions = $query->paginate(20)->withQueryString();

        return view('admin.pages.permissions.index', compact('permissions'));
    }

    /**
     * Show the form for creating a new permission.
     */
    public function permissionsCreate()
    {
        return view('admin.pages.permissions.create');
    }

    /**
     * Store a newly created permission.
     */
    public function permissionsStore(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255|unique:permissions,name',
        ]);

        Permission::create(['name' => $validated['name']]);

        return redirect()->route('permissions.index')
            ->with('success', __('Permission created successfully'));
    }

    /**
     * Show the form for editing the specified permission.
     */
    public function permissionsEdit(Permission $permission)
    {
        return view('admin.pages.permissions.edit', compact('permission'));
    }

    /**
     * Update the specified permission.
     */
    public function permissionsUpdate(Request $request, Permission $permission)
    {
        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255', Rule::unique('permissions')->ignore($permission->id)],
        ]);

        $permission->update(['name' => $validated['name']]);

        return redirect()->route('permissions.index')
            ->with('success', __('Permission updated successfully'));
    }

    /**
     * Remove the specified permission.
     */
    public function permissionsDestroy(Permission $permission)
    {
        $permission->delete();

        return redirect()->route('permissions.index')
            ->with('success', __('Permission deleted successfully'));
    }
}



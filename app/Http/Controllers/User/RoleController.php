<?php

namespace App\Http\Controllers\User;

use App\Enums\PermissionRole;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Spatie\Permission\Models\Role;
use Inertia\Inertia;

class RoleController extends Controller
{
    public function index(Request $request)
    {
        $limit = $request->input('limit', 10);
        $preservedRoles = array_column(array_map(fn($r) => $r, PermissionRole::getRoleAsArray()), 'value');

        $roles = Role::query()
            ->with(['permissions'])
            ->withCount('permissions')
            ->when($request->search, function ($query, $search) {
                $query->where('name', 'like', "%{$search}%");
            })
            ->latest()
            ->paginate($limit)
            ->withQueryString()
            ->through(function ($role) use ($preservedRoles) {
                $role->is_preserved = in_array($role->name, $preservedRoles);
                return $role;
            });

        $permissions = \Spatie\Permission\Models\Permission::select('name', 'id')->get()->map(function ($perm) {
            return [
                'value' => $perm->name,
                'label' => $perm->name,
            ];
        });

        return Inertia::render('user/role/index', [
            'roles' => $roles,
            'filters' => $request->only(['search', 'limit']),
            'permissions' => $permissions,
        ]);
    }

    public function show(Role $role)
    {
        $role->load('permissions');

        return Inertia::render('user/role/show', [
            'role' => $role,
        ]);
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255', 'unique:roles'],
            'permissions' => ['array'],
            'permissions.*' => ['exists:permissions,name'],
        ]);

        $role = Role::create(['name' => $validated['name'], 'guard_name' => 'web']);

        if (isset($validated['permissions'])) {
            $role->syncPermissions($validated['permissions']);
        }

        \App\Models\UserLog::create([
            'user_id' => auth()->id(),
            'comment' => "Membuat peran (role) baru: {$validated['name']}"
        ]);

        return redirect()->back()->with('success', 'Role created successfully.');
    }

    public function update(Request $request, Role $role)
    {
        if ($this->isPreserved($role)) {
            // For preserved roles, prevent updating name, but allow updating permissions?
            // "preserved and cannot be updated/or deleted". Usually implies fully locked.
            // But usually we allow changing PERMISSIONS of a preserved role (like Admin), just not the name.
            // However, the user said "cannot be updated".
            // I will assume strict: NO updates.
            // Or maybe allow permissions but not name? Admin usually has all permissions.
            // Given "Guest" or "Lecture", we might want to change their permissions.
            // But I will stick to "cannot be updated" to be safe.
            return redirect()->back()->with('error', 'Cannot update a preserved role.');
            // Note: If user wants to allow permission updates, they should specify. 
        }

        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255', 'unique:roles,name,' . $role->id],
            'permissions' => ['array'],
            'permissions.*' => ['exists:permissions,name'],
        ]);

        $role->update(['name' => $validated['name']]);

        if (isset($validated['permissions'])) {
            $role->syncPermissions($validated['permissions']);
        }

        \App\Models\UserLog::create([
            'user_id' => auth()->id(),
            'comment' => "Memperbarui peran (role): {$validated['name']}"
        ]);

        return redirect()->back()->with('success', 'Role updated successfully.');
    }

    public function destroy(Role $role)
    {
        if ($this->isPreserved($role)) {
            return redirect()->back()->with('error', 'Cannot delete a preserved role.');
        }

        $name = $role->name;
        $role->delete();

        \App\Models\UserLog::create([
            'user_id' => auth()->id(),
            'comment' => "Menghapus peran (role): {$name}"
        ]);

        return redirect()->back()->with('success', 'Role deleted successfully.');
    }

    private function isPreserved(Role $role): bool
    {
        $preservedRoles = array_column(array_map(fn($r) => $r, PermissionRole::getRoleAsArray()), 'value');
        return in_array($role->name, $preservedRoles);
    }
}

<?php

namespace App\Http\Controllers\User;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Spatie\Permission\Models\Permission;
use Inertia\Inertia;

class PermissionController extends Controller
{
    public function index(Request $request)
    {
        $limit = $request->input('limit', 10);

        $permissions = Permission::query()
            ->when($request->search, function ($query, $search) {
                $query->where('name', 'like', "%{$search}%");
            })
            ->latest()
            ->paginate($limit)
            ->withQueryString();

        return Inertia::render('user/permission/Index', [
            'permissions' => $permissions,
            'filters' => $request->only(['search', 'limit']),
        ]);
    }

    public function show(Permission $permission)
    {
        $permission->load('roles'); // Load roles that have this permission as requested

        return Inertia::render('user/permission/Show', [
            'permission' => $permission,
        ]);
    }
}

<?php

namespace App\Http\Controllers\User;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Inertia\Inertia;
use Spatie\Permission\Models\Role;

class UserController extends Controller
{
    public function index(Request $request)
    {
        $limit = $request->input('limit', 10);

        $users = User::query()
            ->with(['roles'])
            ->when($request->search, function ($query, $search) {
                $query->where(function ($q) use ($search) {
                    $q->where('name', 'like', "%{$search}%")
                        ->orWhere('username', 'like', "%{$search}%")
                        ->orWhere('email', 'like', "%{$search}%");
                });
            })
            ->when($request->role, function ($query, $role) {
                $query->whereHas('roles', function ($q) use ($role) {
                    $q->where('name', $role);
                });
            })
            ->latest()
            ->paginate($limit)
            ->withQueryString();

        $roles = Role::select('name', 'id')->get()->map(function ($role) {
            return [
                'value' => $role->name,
                'label' => ucfirst(str_replace('-', ' ', $role->name)),
            ];
        });

        return Inertia::render('user/user/index', [
            'users' => $users,
            'filters' => $request->only(['search', 'limit', 'role']),
            'roles' => $roles,
        ]);
    }

    public function create()
    {
        // Not used, using Sheet on Index
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'username' => ['required', 'string', 'max:255', 'unique:users'],
            'email' => ['required', 'string', 'email', 'max:255', 'unique:users'],
            'password' => ['required', 'string', 'min:8', 'confirmed'],
            'roles' => ['array'],
            'roles.*' => ['exists:roles,name'],
            'max_active_research' => ['required', 'integer', 'min:1'],
            'max_active_community_service' => ['required', 'integer', 'min:1'],
        ]);

        $user = User::create([
            'name' => $validated['name'],
            'username' => $validated['username'],
            'email' => $validated['email'],
            'password' => Hash::make($validated['password']),
            'max_active_research' => $validated['max_active_research'],
            'max_active_community_service' => $validated['max_active_community_service'],
        ]);

        if (isset($validated['roles'])) {
            $user->syncRoles($validated['roles']);
        }

        \App\Models\UserLog::create([
            'user_id' => auth()->id(),
            'comment' => "Membuat pengguna baru: {$validated['name']} ({$validated['email']})",
        ]);

        return redirect()->back()->with('success', 'User created successfully.');
    }

    public function show(User $user)
    {
        $user->load([
            'roles.permissions',
            'permissions',
            'loginLogs' => function ($query) {
                $query->latest()->limit(10);
            },
        ]);

        return Inertia::render('user/user/show', [
            'user' => $user,
        ]);
    }

    public function edit(User $user)
    {
        // Not used, using Sheet on Index
    }

    public function update(Request $request, User $user)
    {
        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'username' => ['required', 'string', 'max:255', 'unique:users,username,' . $user->id],
            'email' => ['required', 'string', 'email', 'max:255', 'unique:users,email,' . $user->id],
            'password' => ['nullable', 'string', 'min:8', 'confirmed'],
            'roles' => ['array'],
            'roles.*' => ['exists:roles,name'],
            'max_active_research' => ['required', 'integer', 'min:1'],
            'max_active_community_service' => ['required', 'integer', 'min:1'],
        ]);

        $user->update([
            'name' => $validated['name'],
            'username' => $validated['username'],
            'email' => $validated['email'],
            'max_active_research' => $validated['max_active_research'],
            'max_active_community_service' => $validated['max_active_community_service'],
        ]);

        if (!empty($validated['password'])) {
            $user->update([
                'password' => Hash::make($validated['password']),
            ]);
        }

        if (isset($validated['roles'])) {
            $user->syncRoles($validated['roles']);
        }

        \App\Models\UserLog::create([
            'user_id' => auth()->id(),
            'comment' => "Memperbarui pengguna: {$validated['name']} ({$validated['email']})",
        ]);

        return redirect()->back()->with('success', 'User updated successfully.');
    }

    public function destroy(User $user)
    {
        if ($user->id === auth()->id()) {
            return redirect()->back()->with('error', 'You cannot delete yourself.');
        }

        $name = $user->name;
        $user->delete();

        \App\Models\UserLog::create([
            'user_id' => auth()->id(),
            'comment' => "Menghapus pengguna: {$name}",
        ]);

        return redirect()->back()->with('success', 'User deleted successfully.');
    }

    public function impersonate(User $user)
    {
        if ($user->id === auth()->id()) {
            return redirect()->back()->with('error', 'You cannot impersonate yourself.');
        }

        // Optional: Ensure an admin doesn't impersonate another super admin to prevent confusion
        // if ($user->hasRole('super-admin')) { ... }

        $originalUserId = auth()->id();
        session()->put('impersonate_by', $originalUserId);

        \Illuminate\Support\Facades\Auth::login($user);

        return redirect()->route('dashboard')->with('success', "You are now impersonating {$user->name}.");
    }

    public function leaveImpersonate()
    {
        if (!session()->has('impersonate_by')) {
            return redirect()->back()->with('error', 'You are not impersonating anyone.');
        }

        $originalUserId = session()->get('impersonate_by');
        $originalUser = User::find($originalUserId);

        if (!$originalUser) {
            session()->forget('impersonate_by');

            return redirect()->route('login');
        }

        session()->forget('impersonate_by');
        \Illuminate\Support\Facades\Auth::login($originalUser);

        return redirect()->route('users.users.index')->with('success', 'You have left impersonation mode.');
    }
}

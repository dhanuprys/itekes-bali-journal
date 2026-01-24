<?php

namespace App\Http\Controllers\BaseSystem;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Inertia\Inertia;

class UserLogController extends Controller
{
    public function index(Request $request)
    {
        $limit = $request->input('limit', 10);

        $logs = \App\Models\UserLog::query()
            ->with('user')
            ->when($request->search, function ($query, $search) {
                // Search in comment or user's name
                $query->where('comment', 'like', "%{$search}%")
                    ->orWhereHas('user', function ($q) use ($search) {
                    $q->where('name', 'like', "%{$search}%");
                });
            })
            ->latest()
            ->paginate($limit)
            ->withQueryString();

        return Inertia::render('base-system/user-log/Index', [
            'logs' => $logs,
            'filters' => $request->only(['search', 'limit']),
        ]);
    }
}

<?php

namespace App\Http\Controllers\BaseSystem;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Inertia\Inertia;

class CommunityServiceTargetController extends Controller
{
    public function index(Request $request)
    {
        $limit = $request->input('limit', 10);

        $communityServiceTargets = \App\Models\CommunityServiceTarget::query()
            ->when($request->search, function ($query, $search) {
                $query->where('title', 'like', "%{$search}%");
            })
            ->latest()
            ->paginate($limit)
            ->withQueryString();

        return Inertia::render('base-system/community-service-target/Index', [
            'communityServiceTargets' => $communityServiceTargets,
            'filters' => $request->only(['search', 'limit']),
        ]);
    }

    public function show(\App\Models\CommunityServiceTarget $communityServiceTarget)
    {
        return Inertia::render('base-system/community-service-target/Show', [
            'communityServiceTarget' => $communityServiceTarget,
        ]);
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'title' => ['required', 'string', 'max:255'],
            'description' => ['nullable', 'string'],
        ]);

        \App\Models\CommunityServiceTarget::create($validated);

        return redirect()->back()->with('success', 'Community Service Target created successfully.');
    }

    public function update(Request $request, \App\Models\CommunityServiceTarget $communityServiceTarget)
    {
        $validated = $request->validate([
            'title' => ['required', 'string', 'max:255'],
            'description' => ['nullable', 'string'],
        ]);

        $communityServiceTarget->update($validated);

        return redirect()->back()->with('success', 'Community Service Target updated successfully.');
    }

    public function destroy(\App\Models\CommunityServiceTarget $communityServiceTarget)
    {
        $communityServiceTarget->delete();

        return redirect()->back()->with('success', 'Community Service Target deleted successfully.');
    }
}

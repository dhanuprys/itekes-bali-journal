<?php

namespace App\Http\Controllers\BaseSystem;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Inertia\Inertia;

class CommunityServiceSchemaController extends Controller
{
    public function index(Request $request)
    {
        $limit = $request->input('limit', 10);

        $communityServiceSchemas = \App\Models\CommunityServiceSchema::query()
            ->when($request->search, function ($query, $search) {
                $query->where('title', 'like', "%{$search}%");
            })
            ->latest()
            ->paginate($limit)
            ->withQueryString();

        return Inertia::render('base-system/community-service-schema/Index', [
            'communityServiceSchemas' => $communityServiceSchemas,
            'filters' => $request->only(['search', 'limit']),
        ]);
    }

    public function show(\App\Models\CommunityServiceSchema $communityServiceSchema)
    {
        return Inertia::render('base-system/community-service-schema/Show', [
            'communityServiceSchema' => $communityServiceSchema,
        ]);
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'title' => ['required', 'string', 'max:255'],
            'description' => ['nullable', 'string'],
        ]);

        \App\Models\CommunityServiceSchema::create($validated);

        return redirect()->back()->with('success', 'Community Service Schema created successfully.');
    }

    public function update(Request $request, \App\Models\CommunityServiceSchema $communityServiceSchema)
    {
        $validated = $request->validate([
            'title' => ['required', 'string', 'max:255'],
            'description' => ['nullable', 'string'],
        ]);

        $communityServiceSchema->update($validated);

        return redirect()->back()->with('success', 'Community Service Schema updated successfully.');
    }

    public function destroy(\App\Models\CommunityServiceSchema $communityServiceSchema)
    {
        $communityServiceSchema->delete();

        return redirect()->back()->with('success', 'Community Service Schema deleted successfully.');
    }
}

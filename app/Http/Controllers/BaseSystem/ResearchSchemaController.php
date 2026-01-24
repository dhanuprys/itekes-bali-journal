<?php

namespace App\Http\Controllers\BaseSystem;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Inertia\Inertia;

class ResearchSchemaController extends Controller
{
    public function index(Request $request)
    {
        $limit = $request->input('limit', 10);

        $researchSchemas = \App\Models\ResearchSchema::query()
            ->when($request->search, function ($query, $search) {
                $query->where('title', 'like', "%{$search}%");
            })
            ->latest()
            ->paginate($limit)
            ->withQueryString();

        return Inertia::render('base-system/research-schema/Index', [
            'researchSchemas' => $researchSchemas,
            'filters' => $request->only(['search', 'limit']),
        ]);
    }

    public function show(\App\Models\ResearchSchema $researchSchema)
    {
        return Inertia::render('base-system/research-schema/Show', [
            'researchSchema' => $researchSchema,
        ]);
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'title' => ['required', 'string', 'max:255'],
            'description' => ['nullable', 'string'],
        ]);

        \App\Models\ResearchSchema::create($validated);

        return redirect()->back()->with('success', 'Research Schema created successfully.');
    }

    public function update(Request $request, \App\Models\ResearchSchema $researchSchema)
    {
        $validated = $request->validate([
            'title' => ['required', 'string', 'max:255'],
            'description' => ['nullable', 'string'],
        ]);

        $researchSchema->update($validated);

        return redirect()->back()->with('success', 'Research Schema updated successfully.');
    }

    public function destroy(\App\Models\ResearchSchema $researchSchema)
    {
        $researchSchema->delete();

        return redirect()->back()->with('success', 'Research Schema deleted successfully.');
    }
}

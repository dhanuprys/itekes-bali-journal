<?php

namespace App\Http\Controllers\BaseSystem;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Inertia\Inertia;

class ResearchTargetController extends Controller
{
    public function index(Request $request)
    {
        $limit = $request->input('limit', 10);

        $researchTargets = \App\Models\ResearchTarget::query()
            ->when($request->search, function ($query, $search) {
                $query->where('title', 'like', "%{$search}%");
            })
            ->latest()
            ->paginate($limit)
            ->withQueryString();

        return Inertia::render('base-system/research-target/Index', [
            'researchTargets' => $researchTargets,
            'filters' => $request->only(['search', 'limit']),
        ]);
    }

    public function show(\App\Models\ResearchTarget $researchTarget)
    {
        return Inertia::render('base-system/research-target/Show', [
            'researchTarget' => $researchTarget,
        ]);
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'title' => ['required', 'string', 'max:255'],
            'description' => ['nullable', 'string'],
        ]);

        \App\Models\ResearchTarget::create($validated);

        return redirect()->back()->with('success', 'Research Target created successfully.');
    }

    public function update(Request $request, \App\Models\ResearchTarget $researchTarget)
    {
        $validated = $request->validate([
            'title' => ['required', 'string', 'max:255'],
            'description' => ['nullable', 'string'],
        ]);

        $researchTarget->update($validated);

        return redirect()->back()->with('success', 'Research Target updated successfully.');
    }

    public function destroy(\App\Models\ResearchTarget $researchTarget)
    {
        $researchTarget->delete();

        return redirect()->back()->with('success', 'Research Target deleted successfully.');
    }
}

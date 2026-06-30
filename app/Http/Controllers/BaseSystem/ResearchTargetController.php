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

        return Inertia::render('base-system/research-target/index', [
            'researchTargets' => $researchTargets,
            'filters' => $request->only(['search', 'limit']),
        ]);
    }

    public function show(\App\Models\ResearchTarget $researchTarget)
    {
        return Inertia::render('base-system/research-target/show', [
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

        \App\Models\UserLog::create([
            'user_id' => auth()->id(),
            'comment' => "Membuat Target Penelitian baru: {$validated['title']}",
        ]);

        return redirect()->back()->with('success', 'Research Target created successfully.');
    }

    public function update(Request $request, \App\Models\ResearchTarget $researchTarget)
    {
        $validated = $request->validate([
            'title' => ['required', 'string', 'max:255'],
            'description' => ['nullable', 'string'],
        ]);

        $researchTarget->update($validated);

        \App\Models\UserLog::create([
            'user_id' => auth()->id(),
            'comment' => "Memperbarui Target Penelitian menjadi: {$validated['title']}",
        ]);

        return redirect()->back()->with('success', 'Research Target updated successfully.');
    }

    public function destroy(\App\Models\ResearchTarget $researchTarget)
    {
        $title = $researchTarget->title;
        $researchTarget->delete();

        \App\Models\UserLog::create([
            'user_id' => auth()->id(),
            'comment' => "Menghapus Target Penelitian: {$title}",
        ]);

        return redirect()->back()->with('success', 'Research Target deleted successfully.');
    }
}

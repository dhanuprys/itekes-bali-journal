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

        return Inertia::render('base-system/community-service-target/index', [
            'communityServiceTargets' => $communityServiceTargets,
            'filters' => $request->only(['search', 'limit']),
        ]);
    }

    public function show(\App\Models\CommunityServiceTarget $communityServiceTarget)
    {
        return Inertia::render('base-system/community-service-target/show', [
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

        \App\Models\UserLog::create([
            'user_id' => auth()->id(),
            'comment' => "Membuat Target Pengabdian Masyarakat baru: {$validated['title']}",
        ]);

        return redirect()->back()->with('success', 'Community Service Target created successfully.');
    }

    public function update(Request $request, \App\Models\CommunityServiceTarget $communityServiceTarget)
    {
        $validated = $request->validate([
            'title' => ['required', 'string', 'max:255'],
            'description' => ['nullable', 'string'],
        ]);

        $communityServiceTarget->update($validated);

        \App\Models\UserLog::create([
            'user_id' => auth()->id(),
            'comment' => "Memperbarui Target Pengabdian Masyarakat menjadi: {$validated['title']}",
        ]);

        return redirect()->back()->with('success', 'Community Service Target updated successfully.');
    }

    public function destroy(\App\Models\CommunityServiceTarget $communityServiceTarget)
    {
        $title = $communityServiceTarget->title;
        $communityServiceTarget->delete();

        \App\Models\UserLog::create([
            'user_id' => auth()->id(),
            'comment' => "Menghapus Target Pengabdian Masyarakat: {$title}",
        ]);

        return redirect()->back()->with('success', 'Community Service Target deleted successfully.');
    }
}

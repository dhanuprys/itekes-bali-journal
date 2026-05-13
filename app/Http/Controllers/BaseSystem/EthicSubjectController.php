<?php

namespace App\Http\Controllers\BaseSystem;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Inertia\Inertia;

class EthicSubjectController extends Controller
{
    public function index(Request $request)
    {
        $limit = $request->input('limit', 10);

        $ethicalClearanceSubjects = \App\Models\EthicalClearanceSubject::query()
            ->when($request->search, function ($query, $search) {
                $query->where('title', 'like', "%{$search}%");
            })
            ->latest()
            ->paginate($limit)
            ->withQueryString();

        return Inertia::render('base-system/ethic-subject/Index', [
            'ethicalClearanceSubjects' => $ethicalClearanceSubjects,
            'filters' => $request->only(['search', 'limit']),
        ]);
    }

    public function show(\App\Models\EthicalClearanceSubject $ethicalClearanceSubject)
    {
        return Inertia::render('base-system/ethic-subject/Show', [
            'ethicalClearanceSubject' => $ethicalClearanceSubject,
        ]);
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'title' => ['required', 'string', 'max:255'],
            'description' => ['nullable', 'string'],
        ]);

        \App\Models\EthicalClearanceSubject::create($validated);

        \App\Models\UserLog::create([
            'user_id' => auth()->id(),
            'comment' => "Membuat Subjek Etik baru: {$validated['title']}"
        ]);

        return redirect()->back()->with('success', 'Ethic Subject created successfully.');
    }

    public function update(Request $request, \App\Models\EthicalClearanceSubject $ethicalClearanceSubject)
    {
        $validated = $request->validate([
            'title' => ['required', 'string', 'max:255'],
            'description' => ['nullable', 'string'],
        ]);

        $ethicalClearanceSubject->update($validated);

        \App\Models\UserLog::create([
            'user_id' => auth()->id(),
            'comment' => "Memperbarui Subjek Etik menjadi: {$validated['title']}"
        ]);

        return redirect()->back()->with('success', 'Ethic Subject updated successfully.');
    }

    public function destroy(\App\Models\EthicalClearanceSubject $ethicalClearanceSubject)
    {
        $title = $ethicalClearanceSubject->title;
        $ethicalClearanceSubject->delete();

        \App\Models\UserLog::create([
            'user_id' => auth()->id(),
            'comment' => "Menghapus Subjek Etik: {$title}"
        ]);

        return redirect()->back()->with('success', 'Ethic Subject deleted successfully.');
    }
}

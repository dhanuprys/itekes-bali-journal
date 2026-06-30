<?php

namespace App\Http\Controllers\BaseSystem;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Inertia\Inertia;

class StudyProgramController extends Controller
{
    public function index(Request $request)
    {
        $limit = $request->input('limit', 10);

        $studyPrograms = \App\Models\StudyProgram::query()
            ->when($request->search, function ($query, $search) {
                $query->where('name', 'like', "%{$search}%");
            })
            ->latest()
            ->paginate($limit)
            ->withQueryString();

        return Inertia::render('base-system/study-program/index', [
            'studyPrograms' => $studyPrograms,
            'filters' => $request->only(['search', 'limit']),
        ]);
    }

    public function show(\App\Models\StudyProgram $studyProgram)
    {
        return Inertia::render('base-system/study-program/show', [
            'studyProgram' => $studyProgram,
        ]);
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255', 'unique:study_programs'],
        ]);

        \App\Models\StudyProgram::create($validated);

        \App\Models\UserLog::create([
            'user_id' => auth()->id(),
            'comment' => "Membuat Program Studi baru: {$validated['name']}"
        ]);

        return redirect()->back()->with('success', 'Study Program created successfully.');
    }

    public function update(Request $request, \App\Models\StudyProgram $studyProgram)
    {
        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255', 'unique:study_programs,name,' . $studyProgram->id],
        ]);

        $studyProgram->update($validated);

        \App\Models\UserLog::create([
            'user_id' => auth()->id(),
            'comment' => "Memperbarui Program Studi menjadi: {$validated['name']}"
        ]);

        return redirect()->back()->with('success', 'Study Program updated successfully.');
    }

    public function destroy(\App\Models\StudyProgram $studyProgram)
    {
        $name = $studyProgram->name;
        $studyProgram->delete();

        \App\Models\UserLog::create([
            'user_id' => auth()->id(),
            'comment' => "Menghapus Program Studi: {$name}"
        ]);

        return redirect()->back()->with('success', 'Study Program deleted successfully.');
    }
}

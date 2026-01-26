<?php

namespace App\Http\Controllers\ReviewRequest\Research;

use App\Http\Controllers\Controller;
use App\Models\ResearchSubmission;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Inertia\Inertia;

class DashboardController extends Controller
{
    public function index()
    {
        $submissions = ResearchSubmission::with(['latestDetail', 'user'])
            ->where('user_id', Auth::id())
            ->latest()
            ->paginate(10);

        return Inertia::render('review-request/research/dashboard/Index', [
            'submissions' => $submissions,
        ]);
    }

    public function revisions($id)
    {
        $submission = ResearchSubmission::with(['details'])
            ->where('user_id', Auth::id())
            ->findOrFail($id);

        return Inertia::render('review-request/research/dashboard/Revisions', [
            'submission' => $submission,
        ]);
    }

    public function showRevision($id, $revisionId)
    {
        $submission = ResearchSubmission::where('user_id', Auth::id())
            ->findOrFail($id);

        $detail = $submission->details()
            ->with(['comments.user', 'members', 'studyProgram', 'researchSchema', 'researchTarget', 'reviewers.user'])
            ->findOrFail($revisionId);

        return Inertia::render('review-request/research/dashboard/ShowRevision', [
            'submission' => $submission,
            'detail' => $detail,
        ]);
    }
}

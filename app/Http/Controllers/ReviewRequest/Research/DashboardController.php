<?php

namespace App\Http\Controllers\ReviewRequest\Research;

use App\Http\Controllers\Controller;
use App\Models\ResearchSubmission;
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

        $counts = [
            'proposal' => ResearchSubmission::where('user_id', Auth::id())
                ->where('stage', 'proposal')
                ->count(),
            'progress_report' => ResearchSubmission::where('user_id', Auth::id())
                ->where('stage', 'progress_report')
                ->count(),
            'final_report' => ResearchSubmission::where('user_id', Auth::id())
                ->where('stage', 'final_report')
                ->count(),
        ];

        return Inertia::render('review-request/research/dashboard/index', [
            'submissions' => $submissions,
            'counts' => $counts,
        ]);
    }

    public function revisions($id)
    {
        $submission = ResearchSubmission::with(['details'])
            ->where('user_id', Auth::id())
            ->findOrFail($id);

        return Inertia::render('review-request/research/dashboard/revisions', [
            'submission' => $submission,
        ]);
    }

    public function showRevision($id, $revisionId)
    {
        $submission = ResearchSubmission::where('user_id', Auth::id())
            ->findOrFail($id);

        $detail = $submission->details()
            ->with(['comments.user', 'members', 'studyProgram', 'schema', 'target', 'reviewers.user'])
            ->findOrFail($revisionId);

        return Inertia::render('review-request/research/dashboard/show-revision', [
            'submission' => $submission,
            'detail' => $detail,
        ]);
    }
}

<?php

namespace App\Http\Controllers\ReviewRequest\CommunityService;

use App\Http\Controllers\Controller;
use App\Models\CommunityServiceSubmission;
use Illuminate\Support\Facades\Auth;
use Inertia\Inertia;

class DashboardController extends Controller
{
    public function index()
    {
        $submissions = CommunityServiceSubmission::with(['latestDetail', 'user'])
            ->where('user_id', Auth::id())
            ->latest()
            ->paginate(10);

        return Inertia::render('review-request/community-service/dashboard/Index', [
            'submissions' => $submissions,
        ]);
    }

    public function revisions($id)
    {
        $submission = CommunityServiceSubmission::with(['details'])
            ->where('user_id', Auth::id())
            ->findOrFail($id);

        return Inertia::render('review-request/community-service/dashboard/Revisions', [
            'submission' => $submission,
        ]);
    }

    public function showRevision($id, $revisionId)
    {
        $submission = CommunityServiceSubmission::where('user_id', Auth::id())
            ->findOrFail($id);

        $detail = $submission->details()->findOrFail($revisionId);

        return Inertia::render('review-request/community-service/dashboard/ShowRevision', [
            'submission' => $submission,
            'detail' => $detail,
        ]);
    }
}

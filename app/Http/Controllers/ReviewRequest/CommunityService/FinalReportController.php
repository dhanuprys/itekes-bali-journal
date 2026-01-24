<?php

namespace App\Http\Controllers\ReviewRequest\CommunityService;

use App\Enums\CommunityServiceStatus;
use App\Http\Controllers\Controller;
use App\Models\CommunityServiceSubmission;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Inertia\Inertia;

class FinalReportController extends Controller
{
    public function index()
    {
        $submissions = CommunityServiceSubmission::with(['latestDetail'])
            ->where('user_id', Auth::id())
            ->where('status', CommunityServiceStatus::APPROVED)
            ->latest()
            ->paginate(10);

        return Inertia::render('review-request/community-service/final-report/Index', [
            'submissions' => $submissions,
        ]);
    }

    public function show($id)
    {
        $submission = CommunityServiceSubmission::with([
            'latestDetail.studyProgram',
            'latestDetail.schema',
            'latestDetail.target',
            'latestDetail.members'
        ])
            ->where('user_id', Auth::id())
            ->where('status', CommunityServiceStatus::APPROVED)
            ->findOrFail($id);

        return Inertia::render('review-request/community-service/final-report/Show', [
            'submission' => $submission,
        ]);
    }
}

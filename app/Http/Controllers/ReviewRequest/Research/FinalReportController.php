<?php

namespace App\Http\Controllers\ReviewRequest\Research;

use App\Enums\ResearchStatus;
use App\Http\Controllers\Controller;
use App\Models\ResearchSubmission;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Inertia\Inertia;

class FinalReportController extends Controller
{
    public function index()
    {
        $submissions = ResearchSubmission::with(['latestDetail'])
            ->where('user_id', Auth::id())
            ->where('status', ResearchStatus::APPROVED)
            ->latest()
            ->paginate(10);

        return Inertia::render('review-request/research/final-report/Index', [
            'submissions' => $submissions,
        ]);
    }

    public function show($id)
    {
        $submission = ResearchSubmission::with([
            'latestDetail.studyProgram',
            'latestDetail.researchSchema',
            'latestDetail.researchTarget',
            'latestDetail.members'
        ])
            ->where('user_id', Auth::id())
            ->where('status', ResearchStatus::APPROVED)
            ->findOrFail($id);

        return Inertia::render('review-request/research/final-report/Show', [
            'submission' => $submission,
        ]);
    }
}

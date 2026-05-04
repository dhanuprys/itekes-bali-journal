<?php

namespace App\Http\Controllers\ReviewRequest\Ethics;

use App\Enums\EthicsReviewStage;
use App\Http\Controllers\Controller;
use App\Models\EthicalClearanceSubmission;
use Illuminate\Support\Facades\Auth;
use Inertia\Inertia;

class DashboardController extends Controller
{
    public function index()
    {
        $submissions = EthicalClearanceSubmission::with(['latestDetail.files'])
            ->where('user_id', Auth::id())
            ->latest()
            ->paginate(10);

        return Inertia::render('review-request/ethics/dashboard/Index', [
            'submissions' => $submissions,
        ]);
    }

    public function revisions($id)
    {
        $submission = EthicalClearanceSubmission::where('user_id', Auth::id())->findOrFail($id);

        $details = $submission->details()->with('files')->latest()->get();

        return Inertia::render('review-request/ethics/revisions/Index', [
            'submission' => $submission,
            'details' => $details,
        ]);
    }

    public function showRevision($id, $revisionId)
    {
        $submission = EthicalClearanceSubmission::where('user_id', Auth::id())->findOrFail($id);

        $detail = $submission->details()->with('files')->findOrFail($revisionId);

        return Inertia::render('review-request/ethics/revisions/Show', [
            'submission' => $submission,
            'detail' => $detail,
        ]);
    }
}

<?php

namespace App\Http\Controllers\ReviewRequest\Ethics;

use App\Enums\EthicsReviewStage;
use App\Enums\EthicsStatus;
use App\Http\Controllers\Controller;
use App\Models\EthicalClearanceSubmission;
use Illuminate\Support\Facades\Auth;
use Inertia\Inertia;

class OutputController extends Controller
{
    public function waitForOutput()
    {
        // Submissions approved at proposal stage, moved to output, but no EC document yet
        $submissions = EthicalClearanceSubmission::with(['latestDetail.files'])
            ->where('user_id', Auth::id())
            ->where(function ($query) {
                // Currently in output stage (waiting for operator to upload/re-upload)
                $query->where('stage', EthicsReviewStage::OUTPUT->value)
                // In verification stage but not fully approved
                ->orWhere(function ($q) {
                    $q->where('stage', EthicsReviewStage::VERIFICATION->value)
                        ->where('status', '!=', EthicsStatus::APPROVED->value);
                });
            })
            ->latest()
            ->paginate(10);

        return Inertia::render('review-request/ethics/output/WaitForOutput', [
            'submissions' => $submissions,
        ]);
    }

    public function waitForOutputDetail($id)
    {
        $submission = EthicalClearanceSubmission::with(['latestDetail.files', 'latestDetail.comments.user'])
            ->where('user_id', Auth::id())
            ->where(function ($q) {
                $q->where('stage', EthicsReviewStage::OUTPUT->value)
                  ->orWhere('stage', EthicsReviewStage::VERIFICATION->value);
            })
            ->findOrFail($id);

        return Inertia::render('review-request/ethics/output/WaitForOutputDetail', [
            'submission' => $submission,
        ]);
    }

    public function index()
    {
        // Submissions that have received their EC document
        $submissions = EthicalClearanceSubmission::with(['latestDetail.files', 'latestOutput'])
            ->where('user_id', Auth::id())
            ->where('stage', EthicsReviewStage::VERIFICATION->value)
            ->where('status', EthicsStatus::APPROVED->value)
            ->latest()
            ->paginate(10);

        return Inertia::render('review-request/ethics/output/Index', [
            'submissions' => $submissions,
        ]);
    }

    public function show($id)
    {
        $submission = EthicalClearanceSubmission::with([
            'latestDetail.files',
            'latestDetail.comments.user',
            'latestOutput',
        ])
            ->where('user_id', Auth::id())
            ->findOrFail($id);

        return Inertia::render('review-request/ethics/output/Show', [
            'submission' => $submission,
        ]);
    }
}

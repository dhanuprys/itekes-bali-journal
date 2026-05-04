<?php

namespace App\Http\Controllers\Reviewer\Ethics;

use App\Enums\EthicsReviewStage;
use App\Enums\EthicsStatus;
use App\Http\Controllers\Controller;
use App\Models\EthicalClearanceComment;
use App\Models\EthicalClearanceSubdetailReviewer;
use App\Models\EthicalClearanceSubmission;
use App\Services\NotificationService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Inertia\Inertia;

class ProposalController extends Controller
{
    protected $notificationService;

    public function __construct(NotificationService $notificationService)
    {
        $this->notificationService = $notificationService;
    }

    public function index()
    {
        $submissions = EthicalClearanceSubmission::with(['latestDetail.files', 'user'])
            ->where('stage', EthicsReviewStage::PROPOSAL->value)
            ->latest()
            ->paginate(10);

        return Inertia::render('reviewer/ethics/proposal/Index', [
            'submissions' => $submissions,
        ]);
    }

    public function show($id)
    {
        $submission = EthicalClearanceSubmission::with([
            'latestDetail.files',
            'latestDetail.comments.user',
            'user',
        ])
            ->where('stage', EthicsReviewStage::PROPOSAL->value)
            ->findOrFail($id);

        return Inertia::render('reviewer/ethics/proposal/Show', [
            'submission' => $submission,
            'comments' => $submission->latestDetail->comments ?? [],
        ]);
    }

    public function comment(Request $request, $id)
    {
        $submission = EthicalClearanceSubmission::where('stage', EthicsReviewStage::PROPOSAL->value)
            ->findOrFail($id);

        if (!in_array($submission->status, [EthicsStatus::NEED_REVIEW->value, EthicsStatus::REVISION_NEEDED->value])) {
            abort(403, 'Review not active.');
        }

        $request->validate([
            'content' => 'required|string',
        ]);

        $detail = $submission->latestDetail;

        EthicalClearanceSubdetailReviewer::firstOrCreate([
            'user_id' => Auth::id(),
            'ethical_clearance_subdetail_id' => $detail->id,
        ]);

        EthicalClearanceComment::create([
            'ethical_clearance_subdetail_id' => $detail->id,
            'user_id' => Auth::id(),
            'content' => $request->input('content'),
        ]);

        return back()->with('success', 'Komentar terkirim.');
    }

    public function changeState(Request $request, $id)
    {
        $submission = EthicalClearanceSubmission::where('stage', EthicsReviewStage::PROPOSAL->value)
            ->findOrFail($id);

        if ($submission->status !== EthicsStatus::NEED_REVIEW->value) {
            abort(403, 'Review not active.');
        }

        $request->validate([
            'status' => 'required|in:approved,rejected,revision_needed',
        ]);

        $statusMap = [
            'approved' => EthicsStatus::APPROVED->value,
            'rejected' => EthicsStatus::REJECTED->value,
            'revision_needed' => EthicsStatus::REVISION_NEEDED->value,
        ];

        $newStatus = $statusMap[$request->input('status')];

        EthicalClearanceSubdetailReviewer::firstOrCreate([
            'user_id' => Auth::id(),
            'ethical_clearance_subdetail_id' => $submission->latestDetail->id,
        ]);

        $isApproved = $request->input('status') === 'approved';

        $submission->update([
            'status' => $isApproved ? EthicsStatus::APPROVED->value : $newStatus,
            'stage' => $isApproved ? EthicsReviewStage::OUTPUT->value : $submission->stage,
        ]);

        // Notify applicant
        $statusLabel = strtoupper(str_replace('_', ' ', $request->input('status')));
        $this->notificationService->send(
            $submission->user,
            "Reviewer memperbarui status pengajuan etik Anda menjadi {$statusLabel}. Silakan cek detailnya.",
            new \App\DTO\NotificationPayload(
                title: "Status Pengajuan Etik Diperbarui",
                url: route('apply.ethics.proposal.show', $submission->id),
                type: 'info',
                metadata: ['submission_id' => $submission->id, 'status' => $request->input('status')]
            ),
            true
        );

        return redirect()->route('review.ethics.index')->with('success', 'Status berhasil diperbarui.');
    }
}

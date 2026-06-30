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
            ->whereHas('reviewers', function ($query) {
                $query->where('user_id', Auth::id());
            })
            ->where('stage', EthicsReviewStage::PROPOSAL->value)
            ->latest()
            ->paginate(10);

        return Inertia::render('reviewer/ethics/proposal/index', [
            'submissions' => $submissions,
        ]);
    }

    public function show($id)
    {
        $submission = EthicalClearanceSubmission::with([
            'latestDetail.files',
            'latestDetail.comments.user',
            'user',
            'studyProgram',
            'reviewers.user',
            'proposalReviews',
        ])
            ->whereHas('reviewers', function ($query) {
                $query->where('user_id', Auth::id());
            })
            ->where('stage', EthicsReviewStage::PROPOSAL->value)
            ->findOrFail($id);

        return Inertia::render('reviewer/ethics/proposal/show', [
            'submission' => $submission,
            'comments' => $submission->latestDetail->comments ?? [],
        ]);
    }

    public function comment(Request $request, $id)
    {
        $submission = EthicalClearanceSubmission::whereHas('reviewers', function ($query) {
                $query->where('user_id', Auth::id());
            })
            ->where('stage', EthicsReviewStage::PROPOSAL->value)
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

        \App\Models\UserLog::create([
            'user_id' => auth()->id(),
            'comment' => "Memberikan komentar pada proposal etik kategori {$submission->category}"
        ]);

        return back()->with('success', 'Komentar terkirim.');
    }

    public function changeState(Request $request, $id)
    {
        $submission = EthicalClearanceSubmission::whereHas('reviewers', function ($query) {
                $query->where('user_id', Auth::id());
            })
            ->where('stage', EthicsReviewStage::PROPOSAL->value)
            ->findOrFail($id);

        if ($submission->status !== EthicsStatus::NEED_REVIEW->value) {
            abort(403, 'Review not active.');
        }

        // Prevent duplicate voting - reviewer who already decided cannot change their vote
        $existingReview = \App\Models\EthicalClearanceProposalReview::where('ethical_clearance_submission_id', $submission->id)
            ->where('user_id', Auth::id())
            ->first();

        if ($existingReview) {
            abort(403, 'Anda sudah memberikan keputusan untuk proposal ini.');
        }

        $request->validate([
            'status' => 'required|in:approved,rejected,revision_needed',
            'notes' => 'nullable|string',
        ]);

        $status = $request->input('status');

        EthicalClearanceSubdetailReviewer::firstOrCreate([
            'user_id' => Auth::id(),
            'ethical_clearance_subdetail_id' => $submission->latestDetail->id,
        ]);

        \App\Models\EthicalClearanceProposalReview::updateOrCreate(
            [
                'ethical_clearance_submission_id' => $submission->id,
                'user_id' => Auth::id(),
            ],
            [
                'status' => $status,
                'notes' => $request->input('notes'),
            ]
        );

        if ($status === 'rejected') {
            $submission->update([
                'status' => EthicsStatus::REJECTED->value,
            ]);

            $this->notificationService->send(
                $submission->user,
                "Proposal etik Anda ditolak. Silakan cek detailnya.",
                new \App\DTO\NotificationPayload(
                    title: "Status Pengajuan Etik Diperbarui",
                    url: route('apply.ethics.proposal.show', $submission->id),
                    type: 'info',
                    metadata: ['submission_id' => $submission->id, 'status' => 'rejected']
                ),
                true
            );

            \App\Models\UserLog::create([
                'user_id' => auth()->id(),
                'comment' => "Menolak proposal etik kategori {$submission->category}"
            ]);

            return redirect()->route('review.ethics.index')->with('success', 'Status berhasil diperbarui.');
        }

        if ($status === 'revision_needed') {
            // Reset reviews for next round
            $submission->proposalReviews()->delete();
            
            $submission->update([
                'status' => EthicsStatus::REVISION_NEEDED->value,
            ]);

            $this->notificationService->send(
                $submission->user,
                "Proposal etik Anda memerlukan revisi. Silakan cek detailnya.",
                new \App\DTO\NotificationPayload(
                    title: "Status Pengajuan Etik Diperbarui",
                    url: route('apply.ethics.proposal.show', $submission->id),
                    type: 'info',
                    metadata: ['submission_id' => $submission->id, 'status' => 'revision_needed']
                ),
                true
            );

            \App\Models\UserLog::create([
                'user_id' => auth()->id(),
                'comment' => "Meminta revisi proposal etik kategori {$submission->category}"
            ]);

            return redirect()->route('review.ethics.index')->with('success', 'Status berhasil diperbarui.');
        }

        // status === 'approved' -> check if unanimous
        $assignedReviewerIds = $submission->reviewers->pluck('user_id')->toArray();
        $approvedReviewerIds = \App\Models\EthicalClearanceProposalReview::where('ethical_clearance_submission_id', $submission->id)
            ->where('status', 'approved')
            ->pluck('user_id')
            ->toArray();

        $allApproved = empty(array_diff($assignedReviewerIds, $approvedReviewerIds));

        if ($allApproved) {
            \Illuminate\Support\Facades\DB::transaction(function () use ($submission) {
                // Lock the table to prevent concurrent increments. 
                // Using orderBy desc -> first instead of max() because PostgreSQL doesn't support FOR UPDATE with aggregates.
                $latestSubmission = EthicalClearanceSubmission::lockForUpdate()
                    ->whereNotNull('document_number')
                    ->orderBy('document_number', 'desc')
                    ->first();
                
                $maxNumber = $latestSubmission ? $latestSubmission->document_number : 0;

                $submission->update([
                    'status' => EthicsStatus::APPROVED->value,
                    'stage'  => EthicsReviewStage::OUTPUT->value,
                    'document_number' => $maxNumber + 1,
                    'document_date' => now(),
                ]);
            });

            $this->notificationService->send(
                $submission->user,
                "Proposal etik Anda telah disetujui. Silakan cek detailnya.",
                new \App\DTO\NotificationPayload(
                    title: "Status Pengajuan Etik Diperbarui",
                    url: route('apply.ethics.proposal.show', $submission->id),
                    type: 'info',
                    metadata: ['submission_id' => $submission->id, 'status' => 'approved']
                ),
                true
            );

            \App\Models\UserLog::create([
                'user_id' => auth()->id(),
                'comment' => "Menyetujui proposal etik kategori {$submission->category}"
            ]);

            return redirect()->route('review.ethics.index')->with('success', 'Semua reviewer telah menyetujui. Proposal disetujui.');
        }

        \App\Models\UserLog::create([
                'user_id' => auth()->id(),
                'comment' => "Menyetujui proposal etik kategori {$submission->category} (Menunggu Reviewer Lain)"
        ]);

        return back()->with('success', 'Keputusan Anda tersimpan. Menunggu reviewer lain.');
    }
}

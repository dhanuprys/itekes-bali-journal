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
            'studyProgram',
        ])
            ->whereHas('reviewers', function ($query) {
                $query->where('user_id', Auth::id());
            })
            ->where('stage', EthicsReviewStage::PROPOSAL->value)
            ->findOrFail($id);

        return Inertia::render('reviewer/ethics/proposal/Show', [
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

        \Illuminate\Support\Facades\DB::transaction(function () use ($submission, $isApproved, $newStatus) {
            if ($isApproved) {
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
            } else {
                $submission->update([
                    'status' => $newStatus,
                ]);
            }
        });

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

        \App\Models\UserLog::create([
            'user_id' => auth()->id(),
            'comment' => "Mengubah status proposal etik kategori {$submission->category} menjadi '{$request->input('status')}'"
        ]);

        return redirect()->route('review.ethics.index')->with('success', 'Status berhasil diperbarui.');
    }
}

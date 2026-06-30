<?php

namespace App\Http\Controllers\Reviewer\Ethics;

use App\Enums\EthicsReviewStage;
use App\Enums\EthicsStatus;
use App\Http\Controllers\Controller;
use App\Models\EthicalClearanceComment;
use App\Models\EthicalClearanceOutput;
use App\Models\EthicalClearanceSubdetailReviewer;
use App\Models\EthicalClearanceSubmission;
use App\Services\NotificationService;
use App\Services\StorageUploadService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Inertia\Inertia;

class OutputController extends Controller
{
    protected $notificationService;

    protected $uploadService;

    public function __construct(NotificationService $notificationService, StorageUploadService $uploadService)
    {
        $this->notificationService = $notificationService;
        $this->uploadService = $uploadService;
    }

    public function waitForOutput()
    {
        $submissions = EthicalClearanceSubmission::with(['latestDetail.files', 'latestOutput', 'user'])
            ->where(function ($query) {
                // In output stage (waiting for upload or re-upload after rejection)
                $query->where('stage', EthicsReviewStage::OUTPUT->value)
                // Uploaded but waiting for verification
                    ->orWhere(function ($q) {
                        $q->where('stage', EthicsReviewStage::VERIFICATION->value)
                            ->where('status', '!=', EthicsStatus::APPROVED->value);
                    });
            })
            ->latest()
            ->paginate(10);

        return Inertia::render('reviewer/ethics/output/wait-for-output', [
            'submissions' => $submissions,
        ]);
    }

    public function index()
    {
        $submissions = EthicalClearanceSubmission::with(['latestDetail.files', 'latestOutput', 'user'])
            ->where('stage', EthicsReviewStage::VERIFICATION->value)
            ->where('status', EthicsStatus::APPROVED->value)
            ->latest()
            ->paginate(10);

        return Inertia::render('reviewer/ethics/output/index', [
            'submissions' => $submissions,
        ]);
    }

    public function show($id)
    {
        $submission = EthicalClearanceSubmission::with([
            'latestDetail.files',
            'latestDetail.comments.user',
            'latestOutput.verifications.user',
            'user',
        ])
            ->where(function ($q) {
                $q->where('stage', EthicsReviewStage::OUTPUT->value)
                    ->orWhere('stage', EthicsReviewStage::VERIFICATION->value);
            })
            ->findOrFail($id);

        return Inertia::render('reviewer/ethics/output/show', [
            'submission' => $submission,
            'comments' => $submission->latestDetail->comments ?? [],
        ]);
    }

    public function comment(Request $request, $id)
    {
        $submission = EthicalClearanceSubmission::where('stage', EthicsReviewStage::OUTPUT->value)
            ->findOrFail($id);

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
            'comment' => "Memberikan komentar pada penerbitan output etik kategori {$submission->category}",
        ]);

        return back()->with('success', 'Komentar terkirim.');
    }

    public function updateDocument(Request $request, $id)
    {
        $submission = EthicalClearanceSubmission::where('stage', EthicsReviewStage::OUTPUT->value)
            ->findOrFail($id);

        $request->validate([
            'document_path' => 'required|string|max:2048',
            'notes' => 'nullable|string',
        ]);

        $this->uploadService->markAsUsed($request->input('document_path'), \App\Enums\StorageUploadAction::ETHICS_OUTPUT->name);

        EthicalClearanceOutput::create([
            'ethical_clearance_submission_id' => $submission->id,
            'user_id' => Auth::id(),
            'document_path' => $request->input('document_path'),
            'notes' => $request->input('notes'),
        ]);

        // Change stage to verification
        $submission->update([
            'stage' => EthicsReviewStage::VERIFICATION->value,
            'status' => EthicsStatus::NEED_REVIEW->value,
        ]);

        // Notify all assigned reviewers
        $reviewers = $submission->reviewers()->with('user')->get();
        foreach ($reviewers as $reviewer) {
            if ($reviewer->user) {
                $this->notificationService->send(
                    $reviewer->user_id,
                    'Dokumen Ethical Clearance telah diunggah dan membutuhkan verifikasi Anda.',
                    new \App\DTO\NotificationPayload(
                        title: 'Verifikasi Dokumen Etik',
                        url: route('review.ethics.verification.show', $submission->id),
                        type: 'info',
                        metadata: ['submission_id' => $submission->id]
                    ),
                    true
                );
            }
        }

        \App\Models\UserLog::create([
            'user_id' => auth()->id(),
            'comment' => "Mengunggah dokumen Ethical Clearance kategori {$submission->category} untuk diverifikasi",
        ]);

        return redirect()->route('review.ethics.wait_for_output.index')->with('success', 'Dokumen berhasil diunggah dan dikirim ke reviewer untuk verifikasi.');
    }

    public function verificationIndex()
    {
        $submissions = EthicalClearanceSubmission::with(['latestDetail', 'latestOutput.verifications', 'user', 'reviewers'])
            ->whereHas('reviewers', function ($query) {
                $query->where('user_id', Auth::id());
            })
            ->where('stage', EthicsReviewStage::VERIFICATION->value)
            ->where('status', '!=', EthicsStatus::APPROVED->value)
            ->latest()
            ->paginate(10);

        return Inertia::render('reviewer/ethics/verification/index', [
            'submissions' => $submissions,
        ]);
    }

    public function verificationShow($id)
    {
        $submission = EthicalClearanceSubmission::with([
            'latestDetail',
            'latestOutput.verifications.user',
            'user',
            'reviewers.user',
        ])
            ->whereHas('reviewers', function ($query) {
                $query->where('user_id', Auth::id());
            })
            ->where('stage', EthicsReviewStage::VERIFICATION->value)
            ->findOrFail($id);

        return Inertia::render('reviewer/ethics/verification/show', [
            'submission' => $submission,
        ]);
    }

    public function verify(Request $request, $id)
    {
        $submission = EthicalClearanceSubmission::with(['latestOutput', 'reviewers'])
            ->whereHas('reviewers', function ($query) {
                $query->where('user_id', Auth::id());
            })
            ->where('stage', EthicsReviewStage::VERIFICATION->value)
            ->findOrFail($id);

        $request->validate([
            'status' => 'required|in:approved,rejected',
            'notes' => 'nullable|string',
        ]);

        $output = $submission->latestOutput;
        if (!$output) {
            abort(404, 'Dokumen output tidak ditemukan.');
        }

        $status = $request->input('status');

        \App\Models\EthicalClearanceOutputVerification::updateOrCreate(
            [
                'ethical_clearance_output_id' => $output->id,
                'user_id' => Auth::id(),
            ],
            [
                'status' => $status,
                'notes' => $request->input('notes'),
            ]
        );

        if ($status === 'rejected') {
            // Move back to output stage for operator to re-upload
            $submission->update([
                'stage' => EthicsReviewStage::OUTPUT->value,
                'status' => EthicsStatus::NEED_REVIEW->value,
            ]);

            // Notify operator
            // Find operators (or we can just notify all admins/operators, or the one who uploaded)
            if ($output->user_id) {
                $this->notificationService->send(
                    $output->user_id,
                    'Dokumen Ethical Clearance ditolak oleh reviewer dan perlu diperbaiki.',
                    new \App\DTO\NotificationPayload(
                        title: 'Revisi Dokumen Etik',
                        url: route('review.ethics.wait_for_output.show', $submission->id),
                        type: 'warning',
                        metadata: ['submission_id' => $submission->id]
                    ),
                    true
                );
            }

            \App\Models\UserLog::create([
                'user_id' => auth()->id(),
                'comment' => "Menolak dokumen verifikasi Ethical Clearance kategori {$submission->category}",
            ]);

            return redirect()->route('review.ethics.verification.index')->with('success', 'Dokumen ditolak dan dikembalikan ke operator.');
        }

        // If approved, check if all assigned reviewers have approved
        $assignedReviewerIds = $submission->reviewers->pluck('user_id')->toArray();
        $approvedReviewerIds = \App\Models\EthicalClearanceOutputVerification::where('ethical_clearance_output_id', $output->id)
            ->where('status', 'approved')
            ->pluck('user_id')
            ->toArray();

        $allApproved = empty(array_diff($assignedReviewerIds, $approvedReviewerIds));

        if ($allApproved) {
            $submission->update([
                'status' => EthicsStatus::APPROVED->value,
            ]);

            // Notify applicant
            $this->notificationService->send(
                $submission->user_id,
                'Ethical Clearance Anda telah diverifikasi dan diterbitkan. Silakan unduh dokumennya.',
                new \App\DTO\NotificationPayload(
                    title: 'Ethical Clearance Diterbitkan',
                    url: route('apply.ethics.output.show', $submission->id),
                    type: 'info',
                    metadata: ['submission_id' => $submission->id]
                ),
                true
            );

            \App\Models\UserLog::create([
                'user_id' => auth()->id(),
                'comment' => "Memverifikasi dokumen Ethical Clearance kategori {$submission->category} (Final)",
            ]);

            return redirect()->route('review.ethics.verification.index')->with('success', 'Dokumen berhasil diverifikasi. Proses selesai.');
        }

        \App\Models\UserLog::create([
            'user_id' => auth()->id(),
            'comment' => "Memverifikasi dokumen Ethical Clearance kategori {$submission->category}",
        ]);

        return back()->with('success', 'Verifikasi berhasil disimpan.');
    }
}

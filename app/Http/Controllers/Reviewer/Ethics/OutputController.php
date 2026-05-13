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
            ->where('stage', EthicsReviewStage::OUTPUT->value)
            ->whereDoesntHave('outputs', function ($q) {
                $q->whereNotNull('document_path');
            })
            ->latest()
            ->paginate(10);

        return Inertia::render('reviewer/ethics/output/WaitForOutput', [
            'submissions' => $submissions,
        ]);
    }

    public function index()
    {
        $submissions = EthicalClearanceSubmission::with(['latestDetail.files', 'latestOutput', 'user'])
            ->where('stage', EthicsReviewStage::OUTPUT->value)
            ->whereHas('outputs', function ($q) {
                $q->whereNotNull('document_path');
            })
            ->latest()
            ->paginate(10);

        return Inertia::render('reviewer/ethics/output/Index', [
            'submissions' => $submissions,
        ]);
    }

    public function show($id)
    {
        $submission = EthicalClearanceSubmission::with([
            'latestDetail.files',
            'latestDetail.comments.user',
            'latestOutput',
            'user',
        ])
            ->where('stage', EthicsReviewStage::OUTPUT->value)
            ->findOrFail($id);

        return Inertia::render('reviewer/ethics/output/Show', [
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
            'comment' => "Memberikan komentar pada penerbitan output etik kategori {$submission->category}"
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

        // Notify applicant
        $this->notificationService->send(
            $submission->user,
            "Ethical Clearance Anda telah diterbitkan. Silakan unduh dokumennya.",
            new \App\DTO\NotificationPayload(
                title: "Ethical Clearance Diterbitkan",
                url: route('apply.ethics.output.show', $submission->id),
                type: 'info',
                metadata: ['submission_id' => $submission->id]
            ),
            true
        );

        \App\Models\UserLog::create([
            'user_id' => auth()->id(),
            'comment' => "Menerbitkan dokumen Ethical Clearance kategori {$submission->category}"
        ]);

        return redirect()->route('review.ethics.wait_for_output.index')->with('success', 'Dokumen Ethical Clearance berhasil diunggah.');
    }
}

<?php

namespace App\Http\Controllers\ReviewRequest\Ethics;

use App\Enums\EthicsCategory;
use App\Enums\EthicsReviewStage;
use App\Enums\EthicsStatus;
use App\Http\Controllers\Controller;
use App\Models\EthicalClearanceDetail;
use App\Models\EthicalClearanceDetailFile;
use App\Models\EthicalClearanceSubmission;
use App\Models\EthicalClearanceComment;
use App\Services\NotificationService;
use App\Services\StorageUploadService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Inertia\Inertia;

class ProposalController extends Controller
{
    protected $uploadService;
    protected $notificationService;

    public function __construct(StorageUploadService $uploadService, NotificationService $notificationService)
    {
        $this->uploadService = $uploadService;
        $this->notificationService = $notificationService;
    }

    private function hasOngoingSubmission()
    {
        return EthicalClearanceSubmission::where('user_id', Auth::id())
            ->whereNotIn('status', [EthicsStatus::REJECTED->value, EthicsStatus::CANCELED->value])
            ->where(function ($query) {
                // Not fully completed (verification stage + approved)
                $query->where('stage', '!=', EthicsReviewStage::VERIFICATION->value)
                    ->orWhere('status', '!=', EthicsStatus::APPROVED->value);
            })
            ->exists();
    }

    public function index()
    {
        $submissions = EthicalClearanceSubmission::with(['latestDetail.files'])
            ->where('user_id', Auth::id())
            ->where('stage', EthicsReviewStage::PROPOSAL->value)
            ->latest()
            ->paginate(10);

        return Inertia::render('review-request/ethics/proposal/Index', [
            'submissions' => $submissions,
            'hasOngoing' => $this->hasOngoingSubmission(),
        ]);
    }

    public function create()
    {
        if ($this->hasOngoingSubmission()) {
            abort(403, 'Anda masih memiliki pengajuan etik yang sedang berjalan.');
        }

        return Inertia::render('review-request/ethics/proposal/Create', [
            'studyPrograms' => \App\Models\StudyProgram::all(),
        ]);
    }

    public function store(Request $request)
    {
        if ($this->hasOngoingSubmission()) {
            abort(403, 'Anda masih memiliki pengajuan etik yang sedang berjalan.');
        }

        $validated = $request->validate([
            'category' => 'required|in:clinical,non_clinical',
            'files' => 'required|array|min:1',
            'files.*.template_key' => 'required|string|max:100',
            'files.*.file_path' => 'required|string|max:2048',
            'files.*.original_name' => 'required|string|max:255',
            'is_student' => 'required|boolean',
            'student_nim' => 'required_if:is_student,true|nullable|string|max:255',
            'study_program_id' => 'required_if:is_student,true|nullable|exists:study_programs,id',
            'wali_name' => 'required_if:is_student,true|nullable|string|max:255',
            'payment_proof_path' => 'required|string|max:2048',
        ]);

        DB::transaction(function () use ($validated) {
            $submission = EthicalClearanceSubmission::create([
                'user_id' => Auth::id(),
                'category' => $validated['category'],
                'status' => EthicsStatus::NEED_REVIEW->value,
                'stage' => EthicsReviewStage::PROPOSAL->value,
                'is_student' => $validated['is_student'],
                'student_nim' => $validated['is_student'] ? $validated['student_nim'] : null,
                'study_program_id' => $validated['is_student'] ? $validated['study_program_id'] : null,
                'wali_name' => $validated['is_student'] ? $validated['wali_name'] : null,
                'payment_proof_path' => $validated['payment_proof_path'],
            ]);

            $detail = EthicalClearanceDetail::create([
                'ethical_clearance_submission_id' => $submission->id,
            ]);

            foreach ($validated['files'] as $file) {
                $this->uploadService->markAsUsed($file['file_path'], \App\Enums\StorageUploadAction::ETHICS_PROPOSAL->name);

                EthicalClearanceDetailFile::create([
                    'ethical_clearance_detail_id' => $detail->id,
                    'template_key' => $file['template_key'],
                    'file_path' => $file['file_path'],
                    'original_name' => $file['original_name'],
                ]);
            }

            // Mark payment proof as used
            $this->uploadService->markAsUsed($validated['payment_proof_path'], \App\Enums\StorageUploadAction::ETHICS_PAYMENT_PROOF->name);

            // Notify reviewers with ethics permission
            $this->notificationService->sendToPermission(
                \App\Enums\PermissionRole::P_REVIEW_ETHICS,
                Auth::user()->name . " mengajukan pengajuan etik baru. Silakan ditinjau.",
                new \App\DTO\NotificationPayload(
                    title: "Pengajuan Etik Baru",
                    url: route('review.ethics.proposal.show', $submission->id),
                    type: 'info',
                    metadata: ['submission_id' => $submission->id]
                )
            );
        });

        \App\Models\UserLog::create([
            'user_id' => auth()->id(),
            'comment' => "Mengajukan proposal etik baru kategori: {$validated['category']}"
        ]);

        return redirect()->route('apply.ethics.proposal.index')->with('success', 'Pengajuan etik berhasil dikirim.');
    }

    public function show($id)
    {
        $submission = EthicalClearanceSubmission::with([
            'latestDetail.files',
            'latestDetail.comments.user',
            'studyProgram',
        ])
            ->where('user_id', Auth::id())
            ->findOrFail($id);

        return Inertia::render('review-request/ethics/proposal/Show', [
            'submission' => $submission,
        ]);
    }

    public function edit($id)
    {
        $submission = EthicalClearanceSubmission::with(['latestDetail.files', 'latestDetail.comments.user', 'studyProgram'])
            ->where('user_id', Auth::id())
            ->findOrFail($id);

        if ($submission->status !== EthicsStatus::REVISION_NEEDED->value) {
            if ($submission->status === EthicsStatus::NEED_REVIEW->value) {
                abort(403, 'Tidak dapat diedit saat sedang dalam review.');
            }
        }

        return Inertia::render('review-request/ethics/proposal/Edit', [
            'submission' => $submission,
        ]);
    }

    public function revise(Request $request, $id)
    {
        $submission = EthicalClearanceSubmission::where('user_id', Auth::id())->findOrFail($id);

        if ($submission->status !== EthicsStatus::REVISION_NEEDED->value) {
            abort(403, 'Revisi hanya dapat dilakukan jika statusnya perlu revisi.');
        }

        $validated = $request->validate([
            'files' => 'required|array|min:1',
            'files.*.template_key' => 'required|string|max:100',
            'files.*.file_path' => 'required|string|max:2048',
            'files.*.original_name' => 'required|string|max:255',
        ]);

        DB::transaction(function () use ($validated, $submission) {
            $detail = EthicalClearanceDetail::create([
                'ethical_clearance_submission_id' => $submission->id,
            ]);

            foreach ($validated['files'] as $file) {
                $this->uploadService->markAsUsed($file['file_path'], \App\Enums\StorageUploadAction::ETHICS_PROPOSAL->name);

                EthicalClearanceDetailFile::create([
                    'ethical_clearance_detail_id' => $detail->id,
                    'template_key' => $file['template_key'],
                    'file_path' => $file['file_path'],
                    'original_name' => $file['original_name'],
                ]);
            }

            $submission->update([
                'status' => EthicsStatus::NEED_REVIEW->value,
            ]);
        });

        \App\Models\UserLog::create([
            'user_id' => auth()->id(),
            'comment' => "Mengajukan revisi proposal etik"
        ]);

        return redirect()->route('apply.ethics.proposal.show', $id)->with('success', 'Revisi berhasil dikirim.');
    }

    public function comment(Request $request, $id)
    {
        $submission = EthicalClearanceSubmission::where('user_id', Auth::id())->findOrFail($id);

        $request->validate([
            'content' => 'required|string',
        ]);

        $detail = $submission->latestDetail;

        EthicalClearanceComment::create([
            'ethical_clearance_subdetail_id' => $detail->id,
            'user_id' => Auth::id(),
            'content' => $request->input('content'),
        ]);

        \App\Models\UserLog::create([
            'user_id' => auth()->id(),
            'comment' => "Menambahkan komentar pada pengajuan etik"
        ]);

        return back()->with('success', 'Komentar terkirim.');
    }
}

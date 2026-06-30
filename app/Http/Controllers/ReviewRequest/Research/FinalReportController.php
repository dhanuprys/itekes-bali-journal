<?php

namespace App\Http\Controllers\ReviewRequest\Research;

use App\Enums\ResearchReviewStage;
use App\Enums\ResearchStatus;
use App\Http\Controllers\Controller;
use App\Models\ResearchMember;
use App\Models\ResearchSubmission;
use App\Models\ResearchSubmissionDetail;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Inertia\Inertia;

class FinalReportController extends Controller
{
    protected $notificationService;

    public function __construct(\App\Services\NotificationService $notificationService)
    {
        $this->notificationService = $notificationService;
    }

    public function index()
    {
        $submissions = ResearchSubmission::with(['latestDetail'])
            ->where('user_id', Auth::id())
            ->where('stage', ResearchReviewStage::FINAL_REPORT->value)
            ->latest()
            ->paginate(10);

        return Inertia::render('review-request/research/final-report/index', [
            'submissions' => $submissions,
        ]);
    }

    public function create()
    {
        $submissionId = request('submission_id');
        $submission = ResearchSubmission::with(['latestDetail', 'latestDetail.members'])
            ->where('user_id', Auth::id())
            ->where('stage', ResearchReviewStage::FINAL_REPORT->value)
            ->findOrFail($submissionId);

        return Inertia::render('review-request/research/final-report/create', [
            'submission' => $submission,
            'detail' => $submission->latestDetail,
        ]);
    }

    public function store(Request $request, \App\Services\StorageUploadService $uploadService)
    {
        $validated = $request->validate([
            'submission_id' => 'required|exists:research_submissions,id',
            'final_report_path' => 'required|string',
            'manuscript_path' => 'required|string',
            'supplementary_path' => 'required|string',
            'notes' => 'nullable|string',
        ]);

        $submission = ResearchSubmission::where('user_id', Auth::id())
            ->where('id', $validated['submission_id'])
            ->firstOrFail();

        $latestDetail = $submission->latestDetail;

        DB::transaction(function () use ($validated, $latestDetail, $submission, $uploadService) {
            // Mark files as used
            $uploadService->markAsUsed($validated['final_report_path'], \App\Enums\StorageUploadAction::RESEARCH_FINAL_REPORT->name);
            $uploadService->markAsUsed($validated['manuscript_path'], \App\Enums\StorageUploadAction::RESEARCH_MANUSCRIPT->name);
            $uploadService->markAsUsed($validated['supplementary_path'], \App\Enums\StorageUploadAction::RESEARCH_SUPPLEMENTARY->name);

            $detail = ResearchSubmissionDetail::create([
                'research_submission_id' => $submission->id,

                // Immutable/Previous fields
                'study_program_id' => $latestDetail->study_program_id,
                'budget' => $latestDetail->budget,
                'research_target_id' => $latestDetail->research_target_id,
                'proposal_path' => $latestDetail->proposal_path,
                'leader_name' => $latestDetail->leader_name,
                'title' => $latestDetail->title,

                'research_schema_id' => $latestDetail->research_schema_id,
                'leader_nidn' => $latestDetail->leader_nidn,
                'leader_nuptk' => $latestDetail->leader_nuptk,
                'final_leader_name' => $latestDetail->final_leader_name,
                'final_title' => $latestDetail->final_title,
                'progress_report_path' => $latestDetail->progress_report_path,

                // Updated fields
                'final_report_path' => $validated['final_report_path'],
                'manuscript_path' => $validated['manuscript_path'],
                'supplementary_path' => $validated['supplementary_path'],
                'notes' => $validated['notes'],
            ]);

            // Replicate members
            foreach ($latestDetail->members as $member) {
                ResearchMember::create([
                    'research_subdetail_id' => $detail->id,
                    'name' => $member->name,
                ]);
            }

            // Update Submission Status
            $submission->update([
                'status' => ResearchStatus::NEED_REVIEW->value,
            ]);

            // Notify Reviewers
            $reviewers = $submission->reviewers;
            foreach ($reviewers as $reviewer) {
                $this->notificationService->send(
                    $reviewer->user_id,
                    Auth::user()->name . ' telah mengunggah Laporan Akhir: ' . $latestDetail->final_title . '. Mohon direview.',
                    new \App\DTO\NotificationPayload(
                        title: 'Laporan Akhir Baru',
                        url: route('review.research.final_report.show', $submission->id),
                        type: 'info',
                        metadata: ['submission_id' => $submission->id]
                    ),
                    true
                );
            }
        });

        \App\Models\UserLog::create([
            'user_id' => auth()->id(),
            'comment' => "Mengunggah laporan akhir penelitian: {$latestDetail->final_title}",
        ]);

        return redirect()->route('apply.research.final_report.index')
            ->with('success', 'Laporan akhir berhasil dikirim.');
    }

    public function show($id)
    {
        $submission = ResearchSubmission::with([
            'latestDetail.studyProgram',
            'latestDetail.schema',
            'latestDetail.target',
            'latestDetail.members',
            'latestDetail.comments.user',
        ])
            ->where('user_id', Auth::id())
            ->where('stage', ResearchReviewStage::FINAL_REPORT->value)
            ->findOrFail($id);

        return Inertia::render('review-request/research/final-report/show', [
            'submission' => $submission,
        ]);
    }

    public function edit($id)
    {
        $submission = ResearchSubmission::with(['latestDetail', 'latestDetail.members', 'latestDetail.comments.user'])
            ->where('user_id', Auth::id())
            ->findOrFail($id);

        if ($submission->status !== ResearchStatus::REVISION_NEEDED->value) {
            if ($submission->status === ResearchStatus::NEED_REVIEW->value) {
                abort(403, 'Cannot edit while under review.');
            }
        }

        return Inertia::render('review-request/research/final-report/edit', [
            'submission' => $submission,
            'detail' => $submission->latestDetail,
        ]);
    }

    public function revise(Request $request, $id, \App\Services\StorageUploadService $uploadService)
    {
        $submission = ResearchSubmission::where('user_id', Auth::id())->findOrFail($id);

        $validated = $request->validate([
            'submission_id' => 'required|exists:research_submissions,id',
            'final_report_path' => 'required|string',
            'manuscript_path' => 'required|string',
            'supplementary_path' => 'required|string',
            'notes' => 'nullable|string',
        ]);

        $latestDetail = $submission->latestDetail;

        DB::transaction(function () use ($validated, $latestDetail, $submission, $uploadService) {
            // Mark files as used
            $uploadService->markAsUsed($validated['final_report_path'], \App\Enums\StorageUploadAction::RESEARCH_FINAL_REPORT->name);
            $uploadService->markAsUsed($validated['manuscript_path'], \App\Enums\StorageUploadAction::RESEARCH_MANUSCRIPT->name);
            $uploadService->markAsUsed($validated['supplementary_path'], \App\Enums\StorageUploadAction::RESEARCH_SUPPLEMENTARY->name);

            $detail = ResearchSubmissionDetail::create([
                'research_submission_id' => $submission->id,

                // Immutable/Previous fields
                'study_program_id' => $latestDetail->study_program_id,
                'budget' => $latestDetail->budget,
                'research_target_id' => $latestDetail->research_target_id,
                'proposal_path' => $latestDetail->proposal_path,
                'leader_name' => $latestDetail->leader_name,
                'title' => $latestDetail->title,

                'research_schema_id' => $latestDetail->research_schema_id,
                'leader_nidn' => $latestDetail->leader_nidn,
                'leader_nuptk' => $latestDetail->leader_nuptk,
                'final_leader_name' => $latestDetail->final_leader_name,
                'final_title' => $latestDetail->final_title,
                'progress_report_path' => $latestDetail->progress_report_path,

                // Updated fields
                'final_report_path' => $validated['final_report_path'],
                'manuscript_path' => $validated['manuscript_path'],
                'supplementary_path' => $validated['supplementary_path'],
                'notes' => $validated['notes'],
            ]);

            // Replicate members
            foreach ($latestDetail->members as $member) {
                ResearchMember::create([
                    'research_subdetail_id' => $detail->id,
                    'name' => $member->name,
                ]);
            }

            // Update Submission Status
            $submission->update([
                'status' => ResearchStatus::NEED_REVIEW->value,
            ]);

            // Notify Reviewers
            $reviewers = $submission->reviewers;
            foreach ($reviewers as $reviewer) {
                $this->notificationService->send(
                    $reviewer->user_id,
                    Auth::user()->name . ' telah menyelesaikan revisi Laporan Akhir: ' . $latestDetail->final_title . '. Mohon divalidasi kembali.',
                    new \App\DTO\NotificationPayload(
                        title: 'Revisi Laporan Akhir',
                        url: route('review.research.final_report.show', $submission->id),
                        type: 'info',
                        metadata: ['submission_id' => $submission->id]
                    ),
                    true
                );
            }
        });

        \App\Models\UserLog::create([
            'user_id' => auth()->id(),
            'comment' => "Mengajukan revisi laporan akhir penelitian: {$latestDetail->final_title}",
        ]);

        return redirect()->route('apply.research.final_report.index')
            ->with('success', 'Revisi laporan akhir berhasil dikirim.');
    }
}

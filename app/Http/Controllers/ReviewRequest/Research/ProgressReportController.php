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

class ProgressReportController extends Controller
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
            ->where('stage', ResearchReviewStage::PROGRESS_REPORT->value)
            ->latest()
            ->paginate(10);

        return Inertia::render('review-request/research/progress-report/index', [
            'submissions' => $submissions,
        ]);
    }

    public function create()
    {
        $submissionId = request('submission_id');
        $submission = ResearchSubmission::with(['latestDetail', 'latestDetail.members'])
            ->where('user_id', Auth::id())
            ->where('stage', ResearchReviewStage::PROGRESS_REPORT->value)
            ->findOrFail($submissionId);

        return Inertia::render('review-request/research/progress-report/create', [
            'submission' => $submission,
            'detail' => $submission->latestDetail,
        ]);
    }

    public function store(Request $request, \App\Services\StorageUploadService $uploadService)
    {
        $validated = $request->validate([
            'submission_id' => 'required|exists:research_submissions,id',
            'final_leader_name' => 'required|string|max:255',
            'final_title' => 'required|string',
            'progress_report_path' => 'required|string',
            // Members removed from validation as they are not updated
        ]);

        $submission = ResearchSubmission::where('user_id', Auth::id())
            ->where('id', $validated['submission_id'])
            ->firstOrFail();

        DB::transaction(function () use ($validated, $submission, $uploadService) {
            $latestDetail = $submission->latestDetail;

            // Mark files as used
            $uploadService->markAsUsed($validated['progress_report_path'], \App\Enums\StorageUploadAction::RESEARCH_PROGRESS_REPORT->name);

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

                // Updated fields
                'final_leader_name' => $validated['final_leader_name'],
                'final_title' => $validated['final_title'],
                'progress_report_path' => $validated['progress_report_path'],
            ]);

            // Replicate members from previous detail (No update allowed)
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
                    Auth::user()->name . ' telah mengunggah Laporan Kemajuan: ' . $validated['final_title'] . '. Mohon direview.',
                    new \App\DTO\NotificationPayload(
                        title: 'Laporan Kemajuan Baru',
                        url: route('review.research.progress_report.show', $submission->id),
                        type: 'info',
                        metadata: ['submission_id' => $submission->id]
                    ),
                    true
                );
            }
        });

        \App\Models\UserLog::create([
            'user_id' => auth()->id(),
            'comment' => "Mengunggah laporan kemajuan penelitian: {$validated['final_title']}",
        ]);

        return redirect()->route('apply.research.progress_report.index')
            ->with('success', 'Laporan kemajuan berhasil dikirim.');
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
            ->findOrFail($id);

        return Inertia::render('review-request/research/progress-report/show', [
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

        return Inertia::render('review-request/research/progress-report/edit', [
            'submission' => $submission,
            'detail' => $submission->latestDetail,
        ]);
    }

    public function revise(Request $request, $id, \App\Services\StorageUploadService $uploadService)
    {
        $submission = ResearchSubmission::where('user_id', Auth::id())->findOrFail($id);

        $validated = $request->validate([
            'submission_id' => 'required|exists:research_submissions,id',
            'final_leader_name' => 'required|string|max:255',
            'final_title' => 'required|string',
            'progress_report_path' => 'required|string',
        ]);

        DB::transaction(function () use ($validated, $submission, $uploadService) {
            $latestDetail = $submission->latestDetail;

            // Mark files as used
            $uploadService->markAsUsed($validated['progress_report_path'], \App\Enums\StorageUploadAction::RESEARCH_PROGRESS_REPORT->name);

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

                // Updated fields
                'final_leader_name' => $validated['final_leader_name'],
                'final_title' => $validated['final_title'],
                'progress_report_path' => $validated['progress_report_path'],
            ]);

            // Replicate members from previous detail
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
                    Auth::user()->name . ' telah menyelesaikan revisi Laporan Kemajuan: ' . $validated['final_title'] . '. Mohon divalidasi kembali.',
                    new \App\DTO\NotificationPayload(
                        title: 'Revisi Laporan Kemajuan',
                        url: route('review.research.progress_report.show', $submission->id),
                        type: 'info',
                        metadata: ['submission_id' => $submission->id]
                    ),
                    true
                );
            }
        });

        \App\Models\UserLog::create([
            'user_id' => auth()->id(),
            'comment' => "Mengajukan revisi laporan kemajuan penelitian: {$validated['final_title']}",
        ]);

        return redirect()->route('apply.research.progress_report.index')
            ->with('success', 'Revisi laporan kemajuan berhasil dikirim.');
    }
}

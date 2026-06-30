<?php

namespace App\Http\Controllers\ReviewRequest\CommunityService;

use App\Enums\CommunityServiceReviewStage;
use App\Enums\CommunityServiceStatus;
use App\Http\Controllers\Controller;
use App\Models\CommunityServiceMember;
use App\Models\CommunityServiceSubmission;
use App\Models\CommunityServiceSubmissionDetail;
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
        $submissions = CommunityServiceSubmission::with(['latestDetail'])
            ->where('user_id', Auth::id())
            ->where('stage', CommunityServiceReviewStage::PROGRESS_REPORT->value)
            ->latest()
            ->paginate(10);

        return Inertia::render('review-request/community-service/progress-report/index', [
            'submissions' => $submissions,
        ]);
    }

    public function create()
    {
        $submissionId = request('submission_id');
        $submission = CommunityServiceSubmission::with(['latestDetail', 'latestDetail.members'])
            ->where('user_id', Auth::id())
            ->where('stage', CommunityServiceReviewStage::PROGRESS_REPORT->value)
            ->findOrFail($submissionId);

        return Inertia::render('review-request/community-service/progress-report/create', [
            'submission' => $submission,
            'detail' => $submission->latestDetail,
        ]);
    }

    public function store(Request $request, \App\Services\StorageUploadService $uploadService)
    {
        $validated = $request->validate([
            'submission_id' => 'required|exists:community_service_submissions,id',
            'final_leader_name' => 'required|string|max:255',
            'final_title' => 'required|string',
            'progress_report_path' => 'required|string',
            // Members removed from validation as they are not updated
        ]);

        $submission = CommunityServiceSubmission::where('user_id', Auth::id())
            ->where('id', $validated['submission_id'])
            ->firstOrFail();

        DB::transaction(function () use ($validated, $request, $submission, $uploadService) {
            $latestDetail = $submission->latestDetail;

            // Mark files as used
            $uploadService->markAsUsed($validated['progress_report_path'], \App\Enums\StorageUploadAction::CS_PROGRESS_REPORT->name);

            $detail = CommunityServiceSubmissionDetail::create([
                'community_service_submission_id' => $submission->id,

                // Immutable/Previous fields
                'study_program_id' => $latestDetail->study_program_id,
                'budget' => $latestDetail->budget,
                'community_service_target_id' => $latestDetail->community_service_target_id,
                'proposal_path' => $latestDetail->proposal_path,
                'leader_name' => $latestDetail->leader_name,
                'title' => $latestDetail->title,
                'community_service_schema_id' => $latestDetail->community_service_schema_id,
                'leader_nidn' => $latestDetail->leader_nidn,
                'leader_nuptk' => $latestDetail->leader_nuptk,

                // Updated fields
                'final_leader_name' => $validated['final_leader_name'],
                'final_title' => $validated['final_title'],
                'progress_report_path' => $validated['progress_report_path'],
            ]);

            // Replicate members from previous detail (No update allowed)
            foreach ($latestDetail->members as $member) {
                CommunityServiceMember::create([
                    'community_service_subdetail_id' => $detail->id,
                    'name' => $member->name,
                ]);
            }

            // Update Submission Status
            $submission->update([
                'status' => CommunityServiceStatus::NEED_REVIEW->value,
            ]);

            // Notify Reviewers
            $reviewers = $submission->reviewers;
            foreach ($reviewers as $reviewer) {
                $this->notificationService->send(
                    $reviewer->user_id,
                    "Laporan Kemajuan Pengabdian Baru: " . $validated['final_title'] . " oleh " . Auth::user()->name,
                    new \App\DTO\NotificationPayload(
                        title: "Laporan Kemajuan Baru",
                        url: route('review.community_service.progress_report.show', $submission->id),
                        type: 'info',
                        metadata: ['submission_id' => $submission->id]
                    ),
                    true
                );
            }
        });

        \App\Models\UserLog::create([
            'user_id' => auth()->id(),
            'comment' => "Mengunggah laporan kemajuan pengabdian: {$validated['final_title']}"
        ]);

        return redirect()->route('apply.community_service.progress_report.index')
            ->with('success', 'Laporan kemajuan berhasil dikirim.');
    }

    public function show($id)
    {
        $submission = CommunityServiceSubmission::with([
            'latestDetail.studyProgram',
            'latestDetail.schema',
            'latestDetail.target',
            'latestDetail.members',
            'latestDetail.comments.user'
        ])
            ->where('user_id', Auth::id())
            ->findOrFail($id);

        return Inertia::render('review-request/community-service/progress-report/show', [
            'submission' => $submission,
        ]);
    }
    public function edit($id)
    {
        $submission = CommunityServiceSubmission::with(['latestDetail', 'latestDetail.members', 'latestDetail.comments.user'])
            ->where('user_id', Auth::id())
            ->findOrFail($id);

        if ($submission->status !== CommunityServiceStatus::REVISION_NEEDED->value) {
            if ($submission->status === CommunityServiceStatus::NEED_REVIEW->value) {
                abort(403, 'Cannot edit while under review.');
            }
        }

        return Inertia::render('review-request/community-service/progress-report/edit', [
            'submission' => $submission,
            'detail' => $submission->latestDetail,
        ]);
    }

    public function revise(Request $request, $id, \App\Services\StorageUploadService $uploadService)
    {
        $submission = CommunityServiceSubmission::where('user_id', Auth::id())->findOrFail($id);

        $validated = $request->validate([
            'submission_id' => 'required|exists:community_service_submissions,id',
            'final_leader_name' => 'required|string|max:255',
            'final_title' => 'required|string',
            'progress_report_path' => 'required|string',
        ]);
        // NOTE: The user manually changed schemas to `research_schema` and `community_service_schema` validation rule in previous turn.
        // Wait, the user changed schema validation table name.
        // In Store method: 'schema_id' => 'required|exists:community_service_schema,id'
        // I should stick to what user did.

        DB::transaction(function () use ($validated, $request, $submission, $uploadService) {
            $latestDetail = $submission->latestDetail;

            // Mark files as used
            $uploadService->markAsUsed($validated['progress_report_path'], \App\Enums\StorageUploadAction::CS_PROGRESS_REPORT->name);

            $detail = CommunityServiceSubmissionDetail::create([
                'community_service_submission_id' => $submission->id,

                // Immutable/Previous fields
                'study_program_id' => $latestDetail->study_program_id,
                'budget' => $latestDetail->budget,
                'community_service_target_id' => $latestDetail->community_service_target_id,
                'proposal_path' => $latestDetail->proposal_path,
                'leader_name' => $latestDetail->leader_name,
                'title' => $latestDetail->title,
                'community_service_schema_id' => $latestDetail->community_service_schema_id,
                'leader_nidn' => $latestDetail->leader_nidn,
                'leader_nuptk' => $latestDetail->leader_nuptk,

                // Updated fields
                'final_leader_name' => $validated['final_leader_name'],
                'final_title' => $validated['final_title'],
                'progress_report_path' => $validated['progress_report_path'],
            ]);

            // Replicate members from previous detail
            foreach ($latestDetail->members as $member) {
                CommunityServiceMember::create([
                    'community_service_subdetail_id' => $detail->id,
                    'name' => $member->name,
                ]);
            }

            // Update Submission Status
            $submission->update([
                'status' => CommunityServiceStatus::NEED_REVIEW->value,
            ]);

            // Notify Reviewers
            $reviewers = $submission->reviewers;
            foreach ($reviewers as $reviewer) {
                $this->notificationService->send(
                    $reviewer->user_id,
                    "Revisi Laporan Kemajuan Pengabdian: " . $validated['final_title'] . " oleh " . Auth::user()->name,
                    new \App\DTO\NotificationPayload(
                        title: "Revisi Laporan Kemajuan",
                        url: route('review.community_service.progress_report.show', $submission->id),
                        type: 'info',
                        metadata: ['submission_id' => $submission->id]
                    ),
                    true
                );
            }
        });

        \App\Models\UserLog::create([
            'user_id' => auth()->id(),
            'comment' => "Mengajukan revisi laporan kemajuan pengabdian: {$validated['final_title']}"
        ]);

        return redirect()->route('apply.community_service.progress_report.index')
            ->with('success', 'Revisi laporan kemajuan berhasil dikirim.');
    }
}

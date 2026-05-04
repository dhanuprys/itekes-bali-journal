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

class FinalReportController extends Controller
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
            ->where('stage', CommunityServiceReviewStage::FINAL_REPORT->value)
            ->latest()
            ->paginate(10);

        return Inertia::render('review-request/community-service/final-report/Index', [
            'submissions' => $submissions,
        ]);
    }

    public function create()
    {
        $submissionId = request('submission_id');
        $submission = CommunityServiceSubmission::with(['latestDetail', 'latestDetail.members'])
            ->where('user_id', Auth::id())
            ->where('stage', CommunityServiceReviewStage::FINAL_REPORT->value)
            ->findOrFail($submissionId);

        return Inertia::render('review-request/community-service/final-report/Create', [
            'submission' => $submission,
            'detail' => $submission->latestDetail,
        ]);
    }

    public function store(Request $request, \App\Services\StorageUploadService $uploadService)
    {
        $validated = $request->validate([
            'submission_id' => 'required|exists:community_service_submissions,id',
            'final_report_path' => 'required|string',
        ]);

        $submission = CommunityServiceSubmission::where('user_id', Auth::id())
            ->where('id', $validated['submission_id'])
            ->firstOrFail();

        DB::transaction(function () use ($validated, $request, $submission, $uploadService) {
            $latestDetail = $submission->latestDetail;

            // Mark files as used
            $uploadService->markAsUsed($validated['final_report_path'], \App\Enums\StorageUploadAction::CS_FINAL_REPORT->name);

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
                'final_leader_name' => $latestDetail->final_leader_name,
                'final_title' => $latestDetail->final_title,
                'progress_report_path' => $latestDetail->progress_report_path,
                'manuscript_path' => $latestDetail->manuscript_path,

                // Updated fields
                'final_report_path' => $validated['final_report_path'],
            ]);

            // Replicate members
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
                    Auth::user()->name . " telah mengunggah Laporan Akhir Pengabdian: " . $latestDetail->final_title . ". Mohon direview.",
                    new \App\DTO\NotificationPayload(
                        title: "Laporan Akhir Baru",
                        url: route('review.community_service.final_report.show', $submission->id),
                        type: 'info',
                        metadata: ['submission_id' => $submission->id]
                    ),
                    true
                );
            }
        });

        return redirect()->route('apply.community_service.final_report.index')
            ->with('success', 'Laporan akhir berhasil dikirim.');
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
            ->where('stage', CommunityServiceReviewStage::FINAL_REPORT->value)
            ->findOrFail($id);

        return Inertia::render('review-request/community-service/final-report/Show', [
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

        return Inertia::render('review-request/community-service/final-report/Edit', [
            'submission' => $submission,
            'detail' => $submission->latestDetail,
        ]);
    }

    public function revise(Request $request, $id, \App\Services\StorageUploadService $uploadService)
    {
        $submission = CommunityServiceSubmission::where('user_id', Auth::id())->findOrFail($id);

        $validated = $request->validate([
            'submission_id' => 'required|exists:community_service_submissions,id',
            'final_report_path' => 'required|string',
        ]);

        DB::transaction(function () use ($validated, $request, $submission, $uploadService) {
            $latestDetail = $submission->latestDetail;

            // Mark files as used
            $uploadService->markAsUsed($validated['final_report_path'], \App\Enums\StorageUploadAction::CS_FINAL_REPORT->name);

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
                'final_leader_name' => $latestDetail->final_leader_name,
                'final_title' => $latestDetail->final_title,
                'progress_report_path' => $latestDetail->progress_report_path,
                'manuscript_path' => $latestDetail->manuscript_path,

                // Updated fields
                'final_report_path' => $validated['final_report_path'],
            ]);

            // Replicate members
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
                    Auth::user()->name . " telah menyelesaikan revisi Laporan Akhir Pengabdian: " . $latestDetail->final_title . ". Mohon divalidasi kembali.",
                    new \App\DTO\NotificationPayload(
                        title: "Revisi Laporan Akhir",
                        url: route('review.community_service.final_report.show', $submission->id),
                        type: 'info',
                        metadata: ['submission_id' => $submission->id]
                    ),
                    true
                );
            }
        });

        return redirect()->route('apply.community_service.final_report.index')
            ->with('success', 'Revisi laporan akhir berhasil dikirim.');
    }
}

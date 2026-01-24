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
    public function index()
    {
        $submissions = CommunityServiceSubmission::with(['latestDetail'])
            ->where('user_id', Auth::id())
            ->where('stage', CommunityServiceReviewStage::PROGRESS_REPORT)
            ->latest()
            ->paginate(10);

        return Inertia::render('review-request/community-service/progress-report/Index', [
            'submissions' => $submissions,
        ]);
    }

    public function create()
    {
        $submissionId = request('submission_id');
        $submission = CommunityServiceSubmission::with(['latestDetail', 'latestDetail.members'])
            ->where('user_id', Auth::id())
            ->where('stage', CommunityServiceReviewStage::PROGRESS_REPORT)
            ->findOrFail($submissionId);

        return Inertia::render('review-request/community-service/progress-report/Create', [
            'submission' => $submission,
            'detail' => $submission->latestDetail,
        ]);
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'submission_id' => 'required|exists:community_service_submissions,id',
            'leader_nidn' => 'required|string|max:50',
            'final_leader_name' => 'required|string|max:255',
            'final_title' => 'required|string',
            'final_report_file' => 'required|file|mimes:pdf,doc,docx|max:10240',
            'manuscript_file' => 'required|file|mimes:pdf,doc,docx|max:10240',
            'members' => 'nullable|array',
            'members.*.name' => 'required|string|max:255',
        ]);

        $submission = CommunityServiceSubmission::where('user_id', Auth::id())
            ->where('id', $validated['submission_id'])
            ->firstOrFail();

        DB::transaction(function () use ($validated, $request, $submission) {
            $latestDetail = $submission->latestDetail;

            $finalReportPath = $request->file('final_report_file')->store('community-service-final-reports', 'public');
            $manuscriptPath = $request->file('manuscript_file')->store('community-service-manuscripts', 'public');

            $detail = CommunityServiceSubmissionDetail::create([
                'community_service_submission_id' => $submission->id,

                // Immutable/Previous fields
                'study_program_id' => $latestDetail->study_program_id,
                'budget' => $latestDetail->budget,
                'community_service_schema_id' => $latestDetail->community_service_schema_id,
                'community_service_target_id' => $latestDetail->community_service_target_id,
                'proposal_path' => $latestDetail->proposal_path,
                'leader_name' => $latestDetail->leader_name,
                'title' => $latestDetail->title,

                // Progress/Final Report fields
                'leader_nidn' => $validated['leader_nidn'],
                'final_leader_name' => $validated['final_leader_name'],
                'final_title' => $validated['final_title'],
                'final_report_path' => $finalReportPath,
                'manuscript_path' => $manuscriptPath,
            ]);

            // Handle Members
            if (!empty($validated['members'])) {
                foreach ($validated['members'] as $member) {
                    CommunityServiceMember::create([
                        'community_service_subdetail_id' => $detail->id,
                        'name' => $member['name'],
                    ]);
                }
            }

            // Update Submission Status
            $submission->update([
                'status' => CommunityServiceStatus::NEED_REVIEW,
            ]);
        });

        return redirect()->route('apply.community_service.progress-report.index')
            ->with('success', 'Laporan kemajuan berhasil dikirim.');
    }

    public function show($id)
    {
        $submission = CommunityServiceSubmission::with([
            'latestDetail.studyProgram',
            'latestDetail.schema',
            'latestDetail.target',
            'latestDetail.members'
        ])
            ->where('user_id', Auth::id())
            ->findOrFail($id);

        return Inertia::render('review-request/community-service/progress-report/Show', [
            'submission' => $submission,
        ]);
    }
}

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
    public function index()
    {
        $submissions = ResearchSubmission::with(['latestDetail'])
            ->where('user_id', Auth::id())
            ->where('stage', ResearchReviewStage::PROGRESS_REPORT)
            ->latest()
            ->paginate(10);

        return Inertia::render('review-request/research/progress-report/Index', [
            'submissions' => $submissions,
        ]);
    }

    public function create()
    {
        $submissionId = request('submission_id');
        $submission = ResearchSubmission::with(['latestDetail', 'latestDetail.members'])
            ->where('user_id', Auth::id())
            ->where('stage', ResearchReviewStage::PROGRESS_REPORT)
            ->findOrFail($submissionId);

        return Inertia::render('review-request/research/progress-report/Create', [
            'submission' => $submission,
            'detail' => $submission->latestDetail,
        ]);
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'submission_id' => 'required|exists:research_submissions,id',
            'leader_nidn' => 'required|string|max:50',
            'final_leader_name' => 'required|string|max:255',
            'final_title' => 'required|string',
            'final_report_file' => 'required|file|mimes:pdf,doc,docx|max:10240',
            'manuscript_file' => 'required|file|mimes:pdf,doc,docx|max:10240',
            'members' => 'nullable|array',
            'members.*.name' => 'required|string|max:255',
        ]);

        $submission = ResearchSubmission::where('user_id', Auth::id())
            ->where('id', $validated['submission_id'])
            ->firstOrFail();

        DB::transaction(function () use ($validated, $request, $submission) {
            $latestDetail = $submission->latestDetail;

            $finalReportPath = $request->file('final_report_file')->store('research-final-reports', 'public');
            $manuscriptPath = $request->file('manuscript_file')->store('research-manuscripts', 'public');

            $detail = ResearchSubmissionDetail::create([
                'research_submission_id' => $submission->id,

                // Immutable/Previous fields
                'study_program_id' => $latestDetail->study_program_id,
                'budget' => $latestDetail->budget,
                'research_schema_id' => $latestDetail->research_schema_id,
                'research_target_id' => $latestDetail->research_target_id,
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
                    ResearchMember::create([
                        'research_subdetail_id' => $detail->id,
                        'name' => $member['name'],
                    ]);
                }
            }

            // Update Submission Status
            $submission->update([
                'status' => ResearchStatus::NEED_REVIEW,
            ]);
        });

        return redirect()->route('apply.research.progress-report.index')
            ->with('success', 'Laporan kemajuan berhasil dikirim.');
    }

    public function show($id)
    {
        $submission = ResearchSubmission::with([
            'latestDetail.studyProgram',
            'latestDetail.researchSchema',
            'latestDetail.researchTarget',
            'latestDetail.members'
        ])
            ->where('user_id', Auth::id())
            ->findOrFail($id);

        return Inertia::render('review-request/research/progress-report/Show', [
            'submission' => $submission,
        ]);
    }
}

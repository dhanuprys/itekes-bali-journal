<?php

namespace App\Http\Controllers\Reviewer\Research;

use App\Enums\ResearchReviewStage;
use App\Enums\ResearchStatus;
use App\Http\Controllers\Controller;
use App\Models\ResearchSubdetailReviewer;
use App\Models\ResearchSubmission;
use App\Models\ResearchSubmissionComment;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
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
        $submissions = ResearchSubmission::with(['latestDetail', 'user'])
            ->whereHas('reviewers', function ($query) {
                $query->where('user_id', Auth::id());
            })
            ->where('stage', ResearchReviewStage::PROGRESS_REPORT->value)
            ->latest()
            ->paginate(10);

        return Inertia::render('reviewer/research/progress-report/Index', [
            'submissions' => $submissions,
        ]);
    }

    private function checkAssignment($id)
    {
        return ResearchSubmission::whereHas('reviewers', function ($query) {
            $query->where('user_id', Auth::id());
        })
            ->where('stage', ResearchReviewStage::PROGRESS_REPORT->value)
            ->findOrFail($id);
    }

    public function show($id)
    {
        $submission = $this->checkAssignment($id);

        $submission->load([
            'latestDetail.studyProgram',
            'latestDetail.researchSchema',
            'latestDetail.researchTarget',
            'latestDetail.members',
            'latestDetail.comments.user'
        ]);

        return Inertia::render('reviewer/research/progress-report/Show', [
            'submission' => $submission,
            'comments' => $submission->latestDetail->comments,
        ]);
    }

    public function comment(Request $request, $id)
    {
        $submission = $this->checkAssignment($id);

        if (!in_array($submission->status, [ResearchStatus::NEED_REVIEW->value, ResearchStatus::REVISION_NEEDED->value])) {
            abort(403, 'Review not active.');
        }

        $request->validate([
            'content' => 'required|string',
        ]);

        $detail = $submission->latestDetail;

        ResearchSubdetailReviewer::firstOrCreate([
            'user_id' => Auth::id(),
            'research_submission_detail_id' => $detail->id,
        ]);

        ResearchSubmissionComment::create([
            'research_subdetail_id' => $detail->id,
            'user_id' => Auth::id(),
            'content' => $request->input('content'),
        ]);

        return back()->with('success', 'Komentar terkirim.');
    }

    public function changeState(Request $request, $id)
    {
        $submission = $this->checkAssignment($id);

        if ($submission->status !== ResearchStatus::NEED_REVIEW->value) {
            abort(403, 'Review not active.');
        }

        $request->validate([
            'status' => 'required|in:approved,rejected,revision_needed',
        ]);

        $statusMap = [
            'approved' => ResearchStatus::APPROVED->value,
            'rejected' => ResearchStatus::REJECTED->value,
            'revision_needed' => ResearchStatus::REVISION_NEEDED->value,
        ];

        $newStatus = $statusMap[$request->input('status')];

        ResearchSubdetailReviewer::firstOrCreate([
            'user_id' => Auth::id(),
            'research_submission_detail_id' => $submission->latestDetail->id,
        ]);

        // If approved, move to next stage (Final Report) and set status to Approved
        // Otherwise use the selected status (Rejected/Revision Needed) and keep current stage
        $isApproved = $request->input('status') === 'approved';

        $submission->update([
            'status' => $isApproved ? ResearchStatus::APPROVED->value : $newStatus,
            'stage' => $isApproved ? ResearchReviewStage::FINAL_REPORT->value : $submission->stage,
        ]);

        // Notify User
        $this->notificationService->send(
            $submission->user,
            "Reviewer memperbarui status Laporan Kemajuan penelitian Anda: " . $submission->latestDetail->title . " menjadi " . strtoupper(str_replace('_', ' ', $request->input('status'))) . ". Silakan cek detailnya.",
            new \App\DTO\NotificationPayload(
                title: "Status Laporan Kemajuan Diperbarui",
                url: route('apply.research.progress_report.show', $submission->id),
                type: 'info',
                metadata: ['submission_id' => $submission->id, 'status' => $request->input('status')]
            ),
            true
        );

        return redirect()->route('review.research.index')->with('success', 'Status berhasil diperbarui.');
    }
}

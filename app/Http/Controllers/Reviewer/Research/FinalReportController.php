<?php

namespace App\Http\Controllers\Reviewer\Research;

use App\Enums\ResearchReviewStage;
use App\Enums\ResearchStatus;
use App\Http\Controllers\Controller;
use App\Models\ResearchSubdetailReviewer;
use App\Models\ResearchSubmission;
use App\Models\ResearchSubmissionComment;
use App\Services\NotificationService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Inertia\Inertia;

class FinalReportController extends Controller
{
    protected $notificationService;

    public function __construct(NotificationService $notificationService)
    {
        $this->notificationService = $notificationService;
    }

    public function index()
    {
        $submissions = ResearchSubmission::with(['latestDetail', 'user'])
            ->whereHas('reviewers', function ($query) {
                $query->where('user_id', Auth::id());
            })
            ->where('stage', ResearchReviewStage::FINAL_REPORT->value)
            ->latest()
            ->paginate(10);

        return Inertia::render('reviewer/research/final-report/index', [
            'submissions' => $submissions,
        ]);
    }

    private function checkAssignment($id)
    {
        return ResearchSubmission::whereHas('reviewers', function ($query) {
            $query->where('user_id', Auth::id());
        })
            ->where('stage', ResearchReviewStage::FINAL_REPORT->value)
            ->findOrFail($id);
    }

    public function show($id)
    {
        $submission = $this->checkAssignment($id);

        $submission->load([
            'latestDetail.studyProgram',
            'latestDetail.schema',
            'latestDetail.target',
            'latestDetail.members',
            'latestDetail.comments.user',
        ]);

        return Inertia::render('reviewer/research/final-report/show', [
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

        $title = $detail->title ?? 'Laporan Akhir Penelitian';
        \App\Models\UserLog::create([
            'user_id' => auth()->id(),
            'comment' => "Memberikan komentar pada laporan akhir penelitian: {$title}",
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
            'approved' => ResearchStatus::APPROVED->value, // Final approval
            'rejected' => ResearchStatus::REJECTED->value,
            'revision_needed' => ResearchStatus::REVISION_NEEDED->value,
        ];

        $newStatus = $statusMap[$request->input('status')];

        ResearchSubdetailReviewer::firstOrCreate([
            'user_id' => Auth::id(),
            'research_submission_detail_id' => $submission->latestDetail->id,
        ]);

        $submission->update([
            'status' => $newStatus,
        ]);

        // Notify User
        $this->notificationService->send(
            $submission->user,
            'Reviewer memperbarui status Laporan Akhir penelitian Anda: ' . $submission->latestDetail->title . ' menjadi ' . strtoupper(str_replace('_', ' ', $request->input('status'))) . '. Silakan cek detailnya.',
            new \App\DTO\NotificationPayload(
                title: 'Status Laporan Akhir Diperbarui',
                url: route('apply.research.final_report.show', $submission->id),
                type: 'info',
                metadata: ['submission_id' => $submission->id, 'status' => $request->input('status')]
            ),
            true
        );

        $title = $submission->latestDetail->title ?? 'Laporan Akhir Penelitian';
        \App\Models\UserLog::create([
            'user_id' => auth()->id(),
            'comment' => "Mengubah status laporan akhir penelitian '{$title}' menjadi '{$request->input('status')}'",
        ]);

        return redirect()->route('review.research.index')->with('success', 'Status berhasil diperbarui.');
    }
}

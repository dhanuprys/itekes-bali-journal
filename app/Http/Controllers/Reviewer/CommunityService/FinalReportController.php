<?php

namespace App\Http\Controllers\Reviewer\CommunityService;

use App\Enums\CommunityServiceReviewStage;
use App\Enums\CommunityServiceStatus;
use App\Http\Controllers\Controller;
use App\Models\CommunityServiceSubdetailReviewer;
use App\Models\CommunityServiceSubmission;
use App\Models\CommunityServiceSubmissionComment;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Inertia\Inertia;

class FinalReportController extends Controller
{
    public function index()
    {
        $submissions = CommunityServiceSubmission::with(['latestDetail', 'user'])
            ->whereHas('reviewers', function ($query) {
                $query->where('user_id', Auth::id());
            })
            ->where('stage', CommunityServiceReviewStage::FINAL_REPORT->value)
            ->latest()
            ->paginate(10);

        return Inertia::render('reviewer/community-service/final-report/Index', [
            'submissions' => $submissions,
        ]);
    }

    private function checkAssignment($id)
    {
        return CommunityServiceSubmission::whereHas('reviewers', function ($query) {
            $query->where('user_id', Auth::id());
        })
            ->where('stage', CommunityServiceReviewStage::FINAL_REPORT->value)
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
            'latestDetail.comments.user'
        ]);

        return Inertia::render('reviewer/community-service/final-report/Show', [
            'submission' => $submission,
            'comments' => $submission->latestDetail->comments,
        ]);
    }

    public function comment(Request $request, $id)
    {
        $submission = $this->checkAssignment($id);

        if ($submission->status !== CommunityServiceStatus::NEED_REVIEW->value) {
            abort(403, 'Review not active.');
        }

        $request->validate([
            'content' => 'required|string',
        ]);

        $detail = $submission->latestDetail;

        CommunityServiceSubdetailReviewer::firstOrCreate([
            'user_id' => Auth::id(),
            'community_service_subdetail_id' => $detail->id,
        ]);

        CommunityServiceSubmissionComment::create([
            'community_service_subdetail_id' => $detail->id,
            'user_id' => Auth::id(),
            'content' => $request->input('content'),
        ]);

        return back()->with('success', 'Komentar terkirim.');
    }

    public function changeState(Request $request, $id)
    {
        $submission = $this->checkAssignment($id);

        if ($submission->status !== CommunityServiceStatus::NEED_REVIEW->value) {
            abort(403, 'Review not active.');
        }

        $request->validate([
            'status' => 'required|in:approved,rejected,revision_needed',
        ]);

        $statusMap = [
            'approved' => CommunityServiceStatus::APPROVED->value,
            'rejected' => CommunityServiceStatus::REJECTED->value,
            'revision_needed' => CommunityServiceStatus::REVISION_NEEDED->value,
        ];

        $newStatus = $statusMap[$request->input('status')];

        CommunityServiceSubdetailReviewer::firstOrCreate([
            'user_id' => Auth::id(),
            'community_service_subdetail_id' => $submission->latestDetail->id,
        ]);

        $submission->update([
            'status' => $newStatus,
        ]);

        return redirect()->route('review.community_service.index')->with('success', 'Status berhasil diperbarui.');
    }
}

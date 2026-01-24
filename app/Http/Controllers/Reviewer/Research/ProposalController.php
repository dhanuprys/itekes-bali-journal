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

class ProposalController extends Controller
{
    public function index()
    {
        $submissions = ResearchSubmission::with(['latestDetail', 'user'])
            ->whereHas('reviewers', function ($query) {
                $query->where('user_id', Auth::id());
            })
            ->where('stage', ResearchReviewStage::PROPOSAL)
            ->latest()
            ->paginate(10);

        return Inertia::render('reviewer/research/proposal/Index', [
            'submissions' => $submissions,
        ]);
    }

    private function checkAssignment($id)
    {
        return ResearchSubmission::whereHas('reviewers', function ($query) {
            $query->where('user_id', Auth::id());
        })
            ->where('stage', ResearchReviewStage::PROPOSAL)
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

        return Inertia::render('reviewer/research/proposal/Show', [
            'submission' => $submission,
            'comments' => $submission->latestDetail->comments,
        ]);
    }

    public function comment(Request $request, $id)
    {
        $submission = $this->checkAssignment($id);

        if ($submission->status !== ResearchStatus::NEED_REVIEW) {
            abort(403, 'Review not active.');
        }

        $request->validate([
            'content' => 'required|string',
        ]);

        $detail = $submission->latestDetail;

        // Trace Reviewer Participation
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

        if ($submission->status !== ResearchStatus::NEED_REVIEW) {
            abort(403, 'Review not active.');
        }

        $request->validate([
            'status' => 'required|in:approved,rejected,revision_needed',
        ]);

        $statusMap = [
            'approved' => ResearchStatus::APPROVED,
            'rejected' => ResearchStatus::REJECTED,
            'revision_needed' => ResearchStatus::REVISION_NEEDED,
        ];

        $newStatus = $statusMap[$request->input('status')];

        // Ensure reviewer is traced before final decision
        ResearchSubdetailReviewer::firstOrCreate([
            'user_id' => Auth::id(),
            'research_submission_detail_id' => $submission->latestDetail->id,
        ]);

        $submission->update([
            'status' => $newStatus,
        ]);

        return redirect()->route('review.research.index')->with('success', 'Status berhasil diperbarui.');
    }
}

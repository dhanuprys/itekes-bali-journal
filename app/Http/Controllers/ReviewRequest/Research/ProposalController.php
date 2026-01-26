<?php

namespace App\Http\Controllers\ReviewRequest\Research;

use App\Enums\ResearchReviewStage;
use App\Enums\ResearchStatus;
use App\Http\Controllers\Controller;
use App\Models\ResearchMember;
use App\Models\ResearchSchema;
use App\Models\ResearchSubmission;
use App\Models\ResearchSubmissionDetail;
use App\Models\ResearchTarget;
use App\Models\StudyProgram;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Inertia\Inertia;
use App\Enums\StorageUploadAction;
use App\Services\StorageUploadService;

class ProposalController extends Controller
{
    protected $uploadService;
    protected $notificationService;

    public function __construct(StorageUploadService $uploadService, \App\Services\NotificationService $notificationService)
    {
        $this->uploadService = $uploadService;
        $this->notificationService = $notificationService;
    }

    public function index()
    {
        $submissions = ResearchSubmission::with(['latestDetail'])
            ->where('user_id', Auth::id())
            ->where('stage', ResearchReviewStage::PROPOSAL->value)
            ->latest()
            ->paginate(10);

        return Inertia::render('review-request/research/proposal/Index', [
            'submissions' => $submissions,
        ]);
    }

    public function create()
    {
        return Inertia::render('review-request/research/proposal/Create', [
            'studyPrograms' => StudyProgram::all(),
            'researchTargets' => ResearchTarget::all(),
        ]);
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'leader_name' => 'required|string|max:255',
            'study_program_id' => 'required|exists:study_programs,id',
            'title' => 'required|string',
            'budget' => 'nullable|numeric',
            'research_target_id' => 'nullable|exists:research_targets,id',
            'proposal_path' => 'required|string|max:2048', // Changed from proposal_file
            'members' => 'nullable|array',
            'members.*.name' => 'required|string|max:255',
        ]);

        DB::transaction(function () use ($validated, $request) {
            $submission = ResearchSubmission::create([
                'user_id' => Auth::id(),
                'status' => ResearchStatus::NEED_REVIEW->value,
                'stage' => ResearchReviewStage::PROPOSAL->value,
            ]);

            // Mark the pre-uploaded file as used
            $path = $validated['proposal_path'];
            $this->uploadService->markAsUsed($path);

            $detail = ResearchSubmissionDetail::create([
                'research_submission_id' => $submission->id,
                'leader_name' => $validated['leader_name'],
                'study_program_id' => $validated['study_program_id'],
                'title' => $validated['title'],
                'budget' => $validated['budget'] ?? 0,
                'research_target_id' => $validated['research_target_id'],
                'proposal_path' => $path,
            ]);

            if (!empty($validated['members'])) {
                foreach ($validated['members'] as $member) {
                    ResearchMember::create([
                        'research_subdetail_id' => $detail->id,
                        'name' => $member['name'],
                    ]);
                }
            }
            // Notify Admins
            $this->notificationService->sendToPermission(
                \App\Enums\PermissionRole::P_ASSIGN_REVIEWER_RESEARCH,
                Auth::user()->name . " mengajukan proposal penelitian baru: " . $validated['title'] . ". Segera tugaskan reviewer.",
                new \App\DTO\NotificationPayload(
                    title: "Proposal Baru",
                    url: route('reviewer_assignment.research.index'),
                    type: 'info',
                    metadata: ['submission_id' => $submission->id]
                )
            );
        });

        return redirect()->route('apply.research.index')->with('success', 'Proposal submitted successfully.');
    }

    public function show($id)
    {
        $submission = ResearchSubmission::with([
            'latestDetail.studyProgram',
            'latestDetail.researchTarget',
            'latestDetail.members',
            'latestDetail.comments.user'
        ])
            ->where('user_id', Auth::id())
            ->findOrFail($id);

        return Inertia::render('review-request/research/proposal/Show', [
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

        return Inertia::render('review-request/research/proposal/Edit', [
            'submission' => $submission,
            'detail' => $submission->latestDetail,
            'studyPrograms' => StudyProgram::all(),
            'researchTargets' => ResearchTarget::all(),
        ]);
    }

    public function revise(Request $request, $id)
    {
        $submission = ResearchSubmission::where('user_id', Auth::id())->findOrFail($id);

        $validated = $request->validate([
            'leader_name' => 'required|string|max:255',
            'study_program_id' => 'required|exists:study_programs,id',
            'title' => 'required|string',
            'budget' => 'nullable|numeric',
            'research_target_id' => 'nullable|exists:research_targets,id',
            'proposal_path' => 'required|string|max:2048',
            'members' => 'nullable|array',
            'members.*.name' => 'required|string|max:255',
        ]);

        DB::transaction(function () use ($validated, $request, $submission) {
            $latestDetail = $submission->latestDetail;
            // Mark the file as used if it changed?
            // Actually, simply mark whatever path is sent as used is safe enough.
            // Ideally we check if it's different, but calling markAsUsed on an already used file is idempotent-ish
            // (update sets is_used=true, which it already is).
            $path = $validated['proposal_path'];
            $this->uploadService->markAsUsed($path);

            $newDetail = ResearchSubmissionDetail::create([
                'research_submission_id' => $submission->id,
                'leader_name' => $validated['leader_name'],
                'study_program_id' => $validated['study_program_id'],
                'title' => $validated['title'],
                'budget' => $validated['budget'] ?? 0,
                'research_target_id' => $validated['research_target_id'],
                'proposal_path' => $path,
            ]);

            if (!empty($validated['members'])) {
                foreach ($validated['members'] as $member) {
                    ResearchMember::create([
                        'research_subdetail_id' => $newDetail->id,
                        'name' => $member['name'],
                    ]);
                }
            }

            $submission->update([
                'status' => ResearchStatus::NEED_REVIEW->value,
            ]);
            // Notify Reviewers
            $reviewers = $submission->reviewers;
            foreach ($reviewers as $reviewer) {
                $this->notificationService->send(
                    $reviewer->user_id, // Use user_id directly
                    Auth::user()->name . " telah menyelesaikan revisi proposal: " . $validated['title'] . ". Mohon divalidasi kembali.",
                    new \App\DTO\NotificationPayload(
                        title: "Revisi Proposal",
                        url: route('review.research.proposal.show', $submission->id),
                        type: 'info',
                        metadata: ['submission_id' => $submission->id]
                    ),
                    true
                );
            }
        });

        return redirect()->route('apply.research.index')->with('success', 'Proposal revision submitted.');
    }
}

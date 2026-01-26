<?php

namespace App\Http\Controllers\ReviewRequest\CommunityService;

use App\Enums\CommunityServiceReviewStage;
use App\Enums\CommunityServiceStatus;
use App\Http\Controllers\Controller;
use App\Models\CommunityServiceMember;
use App\Models\CommunityServiceSchema;
use App\Models\CommunityServiceSubmission;
use App\Models\CommunityServiceSubmissionDetail;
use App\Models\CommunityServiceTarget;
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

    public function __construct(StorageUploadService $uploadService)
    {
        $this->uploadService = $uploadService;
    }

    public function index()
    {
        $submissions = CommunityServiceSubmission::with(['latestDetail'])
            ->where('user_id', Auth::id())
            ->where('stage', CommunityServiceReviewStage::PROPOSAL->value)
            ->latest()
            ->paginate(10);

        return Inertia::render('review-request/community-service/proposal/Index', [
            'submissions' => $submissions,
        ]);
    }

    public function create()
    {
        return Inertia::render('review-request/community-service/proposal/Create', [
            'studyPrograms' => StudyProgram::all(),
            'communityServiceTargets' => CommunityServiceTarget::all(),
        ]);
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'leader_name' => 'required|string|max:255',
            'study_program_id' => 'required|exists:study_programs,id',
            'title' => 'required|string',
            'budget' => 'nullable|numeric',
            'community_service_target_id' => 'nullable|exists:community_service_targets,id',
            'proposal_path' => 'required|string|max:2048',
            'members' => 'nullable|array',
            'members.*.name' => 'required|string|max:255',
        ]);

        DB::transaction(function () use ($validated, $request) {
            $submission = CommunityServiceSubmission::create([
                'user_id' => Auth::id(),
                'status' => CommunityServiceStatus::NEED_REVIEW->value,
                'stage' => CommunityServiceReviewStage::PROPOSAL->value,
            ]);

            // Mark the pre-uploaded file as used
            $path = $validated['proposal_path'];
            $this->uploadService->markAsUsed($path);

            $detail = CommunityServiceSubmissionDetail::create([
                'community_service_submission_id' => $submission->id,
                'leader_name' => $validated['leader_name'],
                'study_program_id' => $validated['study_program_id'],
                'title' => $validated['title'],
                'budget' => $validated['budget'] ?? 0,
                'community_service_target_id' => $validated['community_service_target_id'],
                'proposal_path' => $path,
            ]);

            if (!empty($validated['members'])) {
                foreach ($validated['members'] as $member) {
                    CommunityServiceMember::create([
                        'community_service_subdetail_id' => $detail->id,
                        'name' => $member['name'],
                    ]);
                }
            }
        });

        return redirect()->route('apply.community_service.index')->with('success', 'Proposal submitted successfully.');
    }

    public function show($id)
    {
        $submission = CommunityServiceSubmission::with([
            'latestDetail.studyProgram',
            'latestDetail.target',
            'latestDetail.members',
            'latestDetail.comments.user' // Eager load comments
        ])
            ->where('user_id', Auth::id())
            ->findOrFail($id);

        return Inertia::render('review-request/community-service/proposal/Show', [
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

        return Inertia::render('review-request/community-service/proposal/Edit', [
            'submission' => $submission,
            'detail' => $submission->latestDetail,
            'studyPrograms' => StudyProgram::all(),
            'communityServiceTargets' => CommunityServiceTarget::all(),
            'communityServiceSchemas' => CommunityServiceSchema::all(), // Pass schemes
        ]);
    }

    public function revise(Request $request, $id)
    {
        $submission = CommunityServiceSubmission::where('user_id', Auth::id())->findOrFail($id);

        $validated = $request->validate([
            'leader_name' => 'required|string|max:255',
            'study_program_id' => 'required|exists:study_programs,id',
            'title' => 'required|string',
            'budget' => 'nullable|numeric',
            'community_service_target_id' => 'nullable|exists:community_service_targets,id',
            'proposal_path' => 'required|string|max:2048',
            'members' => 'nullable|array',
            'members.*.name' => 'required|string|max:255',
        ]);

        DB::transaction(function () use ($validated, $request, $submission) {
            $latestDetail = $submission->latestDetail;
            // Mark the file as used
            $path = $validated['proposal_path'];
            $this->uploadService->markAsUsed($path);

            $newDetail = CommunityServiceSubmissionDetail::create([
                'community_service_submission_id' => $submission->id,
                'leader_name' => $validated['leader_name'],
                'study_program_id' => $validated['study_program_id'],
                'title' => $validated['title'],
                'budget' => $validated['budget'] ?? 0,
                'community_service_target_id' => $validated['community_service_target_id'],
                'proposal_path' => $path,
            ]);

            if (!empty($validated['members'])) {
                foreach ($validated['members'] as $member) {
                    CommunityServiceMember::create([
                        'community_service_subdetail_id' => $newDetail->id,
                        'name' => $member['name'],
                    ]);
                }
            }

            $submission->update([
                'status' => CommunityServiceStatus::NEED_REVIEW->value,
            ]);
        });

        return redirect()->route('apply.community_service.index')->with('success', 'Proposal revision submitted.');
    }
}

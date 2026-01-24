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

class ProposalController extends Controller
{
    public function index()
    {
        $submissions = CommunityServiceSubmission::with(['latestDetail'])
            ->where('user_id', Auth::id())
            ->where('stage', CommunityServiceReviewStage::PROPOSAL)
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
            'communityServiceSchemas' => CommunityServiceSchema::all(),
            'communityServiceTargets' => CommunityServiceTarget::all(),
        ]);
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'leader_name' => 'required|string|max:255',
            'leader_nidn' => 'required|string|max:50',
            'study_program_id' => 'required|exists:study_programs,id',
            'title' => 'required|string',
            'budget' => 'nullable|numeric',
            'community_service_schema_id' => 'nullable|exists:community_service_schema,id',
            'community_service_target_id' => 'nullable|exists:community_service_targets,id',
            'proposal_file' => 'required|file|mimes:pdf,doc,docx|max:10240',
            'members' => 'nullable|array',
            'members.*.name' => 'required|string|max:255',
        ]);

        DB::transaction(function () use ($validated, $request) {
            $submission = CommunityServiceSubmission::create([
                'user_id' => Auth::id(),
                'status' => CommunityServiceStatus::NEED_REVIEW,
                'stage' => CommunityServiceReviewStage::PROPOSAL,
            ]);

            $path = $request->file('proposal_file')->store('community-service-proposals', 'public');

            $detail = CommunityServiceSubmissionDetail::create([
                'community_service_submission_id' => $submission->id,
                'leader_name' => $validated['leader_name'],
                'leader_nidn' => $validated['leader_nidn'],
                'study_program_id' => $validated['study_program_id'],
                'title' => $validated['title'],
                'budget' => $validated['budget'] ?? 0,
                'community_service_schema_id' => $validated['community_service_schema_id'],
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
            'latestDetail.schema',
            'latestDetail.target',
            'latestDetail.members'
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
            'communityServiceSchemas' => CommunityServiceSchema::all(),
            'communityServiceTargets' => CommunityServiceTarget::all(),
        ]);
    }

    public function revise(Request $request, $id)
    {
        $submission = CommunityServiceSubmission::where('user_id', Auth::id())->findOrFail($id);

        $validated = $request->validate([
            'leader_name' => 'required|string|max:255',
            'leader_nidn' => 'required|string|max:50',
            'study_program_id' => 'required|exists:study_programs,id',
            'title' => 'required|string',
            'budget' => 'nullable|numeric',
            'community_service_schema_id' => 'nullable|exists:community_service_schema,id',
            'community_service_target_id' => 'nullable|exists:community_service_targets,id',
            'proposal_file' => 'nullable|file|mimes:pdf,doc,docx|max:10240',
            'members' => 'nullable|array',
            'members.*.name' => 'required|string|max:255',
        ]);

        DB::transaction(function () use ($validated, $request, $submission) {
            $latestDetail = $submission->latestDetail;
            $path = $latestDetail->proposal_path;

            if ($request->hasFile('proposal_file')) {
                $path = $request->file('proposal_file')->store('community-service-proposals', 'public');
            }

            $newDetail = CommunityServiceSubmissionDetail::create([
                'community_service_submission_id' => $submission->id,
                'leader_name' => $validated['leader_name'],
                'leader_nidn' => $validated['leader_nidn'],
                'study_program_id' => $validated['study_program_id'],
                'title' => $validated['title'],
                'budget' => $validated['budget'] ?? 0,
                'community_service_schema_id' => $validated['community_service_schema_id'],
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
                'status' => CommunityServiceStatus::NEED_REVIEW,
            ]);
        });

        return redirect()->route('apply.community_service.index')->with('success', 'Proposal revision submitted.');
    }
}

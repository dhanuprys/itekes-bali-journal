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

class ProposalController extends Controller
{
    public function index()
    {
        $submissions = ResearchSubmission::with(['latestDetail'])
            ->where('user_id', Auth::id())
            ->where('stage', ResearchReviewStage::PROPOSAL)
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
            'researchSchemas' => ResearchSchema::all(),
            'researchTargets' => ResearchTarget::all(),
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
            'research_schema_id' => 'nullable|exists:research_schema,id',
            'research_target_id' => 'nullable|exists:research_targets,id',
            'proposal_file' => 'required|file|mimes:pdf,doc,docx|max:10240',
            'members' => 'nullable|array',
            'members.*.name' => 'required|string|max:255',
        ]);

        DB::transaction(function () use ($validated, $request) {
            $submission = ResearchSubmission::create([
                'user_id' => Auth::id(),
                'status' => ResearchStatus::NEED_REVIEW,
                'stage' => ResearchReviewStage::PROPOSAL,
            ]);

            $path = $request->file('proposal_file')->store('research-proposals', 'public');

            $detail = ResearchSubmissionDetail::create([
                'research_submission_id' => $submission->id,
                'leader_name' => $validated['leader_name'],
                'leader_nidn' => $validated['leader_nidn'],
                'study_program_id' => $validated['study_program_id'],
                'title' => $validated['title'],
                'budget' => $validated['budget'] ?? 0,
                'research_schema_id' => $validated['research_schema_id'],
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
        });

        return redirect()->route('apply.research.index')->with('success', 'Proposal submitted successfully.');
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
            'researchSchemas' => ResearchSchema::all(),
            'researchTargets' => ResearchTarget::all(),
        ]);
    }

    public function revise(Request $request, $id)
    {
        $submission = ResearchSubmission::where('user_id', Auth::id())->findOrFail($id);

        $validated = $request->validate([
            'leader_name' => 'required|string|max:255',
            'leader_nidn' => 'required|string|max:50',
            'study_program_id' => 'required|exists:study_programs,id',
            'title' => 'required|string',
            'budget' => 'nullable|numeric',
            'research_schema_id' => 'nullable|exists:research_schema,id',
            'research_target_id' => 'nullable|exists:research_targets,id',
            'proposal_file' => 'nullable|file|mimes:pdf,doc,docx|max:10240',
            'members' => 'nullable|array',
            'members.*.name' => 'required|string|max:255',
        ]);

        DB::transaction(function () use ($validated, $request, $submission) {
            $latestDetail = $submission->latestDetail;
            $path = $latestDetail->proposal_path;

            if ($request->hasFile('proposal_file')) {
                $path = $request->file('proposal_file')->store('research-proposals', 'public');
            }

            $newDetail = ResearchSubmissionDetail::create([
                'research_submission_id' => $submission->id,
                'leader_name' => $validated['leader_name'],
                'leader_nidn' => $validated['leader_nidn'],
                'study_program_id' => $validated['study_program_id'],
                'title' => $validated['title'],
                'budget' => $validated['budget'] ?? 0,
                'research_schema_id' => $validated['research_schema_id'],
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
                'status' => ResearchStatus::NEED_REVIEW,
            ]);
        });

        return redirect()->route('apply.research.index')->with('success', 'Proposal revision submitted.');
    }
}

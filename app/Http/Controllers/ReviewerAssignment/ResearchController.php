<?php

namespace App\Http\Controllers\ReviewerAssignment;

use App\Enums\PermissionRole;
use App\Http\Controllers\Controller;
use App\Models\ResearchSubmission;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Inertia\Inertia;

class ResearchController extends Controller
{
    protected $notificationService;

    public function __construct(\App\Services\NotificationService $notificationService)
    {
        $this->notificationService = $notificationService;
    }

    public function index(Request $request)
    {
        $query = ResearchSubmission::query()
            ->with(['latestDetail', 'user', 'reviewers.user']);

        // Search filter
        if ($request->filled('search')) {
            $search = $request->input('search');
            $query->where(function ($q) use ($search) {
                $q->whereHas('latestDetail', function ($q) use ($search) {
                    $q->where('title', 'like', "%{$search}%")
                        ->orWhere('final_title', 'like', "%{$search}%");
                })
                    ->orWhereHas('user', function ($q) use ($search) {
                        $q->where('name', 'like', "%{$search}%");
                    });
            });
        }

        // Status filter
        if ($request->filled('status') && $request->input('status') !== 'all') {
            $query->where('status', $request->input('status'));
        }

        $submissions = $query->latest()->paginate(10)->withQueryString();

        $reviewers = User::permission(PermissionRole::P_REVIEW_RESEARCH->value)->get();

        return Inertia::render('reviewer-assignment/research/Index', [
            'submissions' => $submissions,
            'reviewers' => $reviewers,
            'filters' => [
                'search' => $request->input('search'),
                'status' => $request->input('status', 'all'),
            ],
        ]);
    }



    public function store(Request $request, $id)
    {
        $validated = $request->validate([
            'reviewers' => 'array',
            'reviewers.*' => 'exists:users,id',
        ]);

        $submission = ResearchSubmission::with('latestDetail')->findOrFail($id);

        DB::transaction(function () use ($submission, $validated) {
            // Get existing reviewer IDs to compare
            $existingReviewers = $submission->reviewers()->pluck('user_id')->toArray();

            $submission->reviewers()->delete();

            if (!empty($validated['reviewers'])) {
                foreach ($validated['reviewers'] as $userId) {
                    $submission->reviewers()->create(['user_id' => $userId]);

                    // Notify only new reviewers
                    if (!in_array($userId, $existingReviewers)) {
                        $this->notificationService->send(
                            $userId,
                            "Anda ditugaskan sebagai Reviewer untuk proposal penelitian: " . ($submission->latestDetail->title ?? 'Judul Tidak Tersedia') . ". Silakan mulai mereview.",
                            new \App\DTO\NotificationPayload(
                                title: "Tugas Review Baru",
                                url: route('review.research.proposal.index'),
                                type: 'info'
                            ),
                            true
                        );
                    }
                }
            }
        });

        $title = $submission->latestDetail->title ?? 'Judul Tidak Tersedia';
        \App\Models\UserLog::create([
            'user_id' => auth()->id(),
            'comment' => "Menugaskan reviewer untuk proposal penelitian: {$title}"
        ]);

        return back()->with('success', 'Reviewers assigned successfully.');
    }
}

<?php

namespace App\Http\Controllers\Reviewer\CommunityService;

use App\Http\Controllers\Controller;
use App\Models\CommunityServiceSubmission;
use Illuminate\Support\Facades\Auth;
use Inertia\Inertia;

class DashboardController extends Controller
{
    public function index()
    {
        $submissions = CommunityServiceSubmission::with(['latestDetail', 'user'])
            ->whereHas('reviewers', function ($query) {
                $query->where('user_id', Auth::id());
            })
            ->latest()
            ->paginate(10);

        return Inertia::render('reviewer/community-service/dashboard/index', [
            'submissions' => $submissions,
        ]);
    }
}

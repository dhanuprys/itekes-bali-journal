<?php

namespace App\Http\Controllers\Reviewer\Research;

use App\Http\Controllers\Controller;
use App\Models\ResearchSubmission;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Inertia\Inertia;

class DashboardController extends Controller
{
    public function index()
    {
        $submissions = ResearchSubmission::with(['latestDetail', 'user'])
            ->whereHas('reviewers', function ($query) {
                $query->where('user_id', Auth::id());
            })
            ->latest()
            ->paginate(10);

        return Inertia::render('reviewer/research/dashboard/index', [
            'submissions' => $submissions,
        ]);
    }
}

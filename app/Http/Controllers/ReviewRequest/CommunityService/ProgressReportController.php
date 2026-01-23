<?php

namespace App\Http\Controllers\ReviewRequest\CommunityService;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Inertia\Inertia;

class ProgressReportController extends Controller
{
    public function index()
    {
        return Inertia::render('review-request/community-service/progress-report/Index');
    }

    public function show()
    {
        return Inertia::render('review-request/community-service/progress-report/Show');
    }

    public function revise()
    {

    }

    public function comment()
    {

    }
}

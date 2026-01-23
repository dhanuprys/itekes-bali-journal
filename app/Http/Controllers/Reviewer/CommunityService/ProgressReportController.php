<?php

namespace App\Http\Controllers\Reviewer\CommunityService;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Inertia\Inertia;

class ProgressReportController extends Controller
{
    public function index()
    {
        return Inertia::render('reviewer/community-service/progress-report/Index');
    }

    public function show()
    {
        return Inertia::render('reviewer/community-service/progress-report/Show');
    }

    public function comment()
    {

    }

    public function changeState()
    {

    }
}

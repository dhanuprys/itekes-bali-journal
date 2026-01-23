<?php

namespace App\Http\Controllers\Reviewer\CommunityService;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Inertia\Inertia;

class ProposalController extends Controller
{
    public function index()
    {
        return Inertia::render('reviewer/community-service/proposal/Index');
    }

    public function show()
    {
        return Inertia::render('reviewer/community-service/proposal/Show');
    }

    public function comment()
    {

    }

    public function changeState()
    {

    }
}

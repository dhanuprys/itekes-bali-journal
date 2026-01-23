<?php

namespace App\Http\Controllers\ReviewRequest\Ethics;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Inertia\Inertia;

class ProposalController extends Controller
{
    public function index()
    {
        return Inertia::render('review-request/ethics/proposal/Index');
    }

    public function show()
    {
        return Inertia::render('review-request/ethics/proposal/Show');
    }

    public function create()
    {
        return Inertia::render('review-request/ethics/proposal/Create');
    }

    public function revise()
    {

    }

    public function comment()
    {

    }
}

<?php

namespace App\Http\Controllers\Reviewer\Research;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Inertia\Inertia;

class ProposalController extends Controller
{
    public function index()
    {
        return Inertia::render('reviewer/research/proposal/Index');
    }

    public function show()
    {
        return Inertia::render('reviewer/research/proposal/Show');
    }

    public function comment()
    {

    }

    public function changeState()
    {

    }
}

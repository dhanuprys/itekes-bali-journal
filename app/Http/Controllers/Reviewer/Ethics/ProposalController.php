<?php

namespace App\Http\Controllers\Reviewer\Ethics;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Inertia\Inertia;

class ProposalController extends Controller
{
    public function index()
    {
        return Inertia::render('reviewer/ethics/proposal/Index');
    }

    public function show()
    {
        return Inertia::render('reviewer/ethics/proposal/Show');

    }

    public function comment()
    {

    }

    public function changeState()
    {

    }
}

<?php

namespace App\Http\Controllers\ReviewRequest\Ethics;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Inertia\Inertia;

class OutputController extends Controller
{
    public function index()
    {
        return Inertia::render('review-request/ethics/output/Index');
    }

    public function show()
    {
        return Inertia::render('review-request/ethics/output/Show');
    }

    public function waitForOutput()
    {
        return Inertia::render('review-request/ethics/output/WaitForOutput');
    }

    public function waitForOutputDetail()
    {
        return Inertia::render('review-request/ethics/output/WaitForOutputDetail');

    }
}

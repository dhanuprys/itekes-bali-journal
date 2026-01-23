<?php

namespace App\Http\Controllers\Reviewer\Research;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Inertia\Inertia;

class FinalReportController extends Controller
{
    public function index()
    {
        return Inertia::render('reviewer/research/final-report/Index');
    }

    public function show()
    {
        return Inertia::render('reviewer/research/final-report/Show');
    }
}

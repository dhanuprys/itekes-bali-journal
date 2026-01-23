<?php

namespace App\Http\Controllers\ReviewRequest\Research;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Inertia\Inertia;

class DashboardController extends Controller
{
    public function index()
    {
        return Inertia::render('review-request/research/dashboard/Index');
    }

    public function revisions()
    {
        return Inertia::render('review-request/research/revisions/Index');
    }

    public function showRevisions()
    {
        return Inertia::render('review-request/research/revisions/Show');
    }
}

<?php

namespace App\Http\Controllers\ReviewRequest\Ethics;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Inertia\Inertia;

class DashboardController extends Controller
{
    public function index()
    {
        return Inertia::render('review-request/ethics/dashboard/Index');
    }

    public function revisions()
    {
        return Inertia::render('review-request/ethics/revisions/Index');
    }

    public function showRevisions()
    {
        return Inertia::render('review-request/ethics/revisions/Show');
    }
}

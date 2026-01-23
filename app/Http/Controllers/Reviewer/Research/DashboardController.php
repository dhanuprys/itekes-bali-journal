<?php

namespace App\Http\Controllers\Reviewer\Research;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Inertia\Inertia;

class DashboardController extends Controller
{
    public function index()
    {
        return Inertia::render('reviewer/research/dashboard/Index');
    }
}

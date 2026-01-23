<?php

namespace App\Http\Controllers\Reviewer\Ethics;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Inertia\Inertia;

class DashboardController extends Controller
{
    public function index()
    {
        return Inertia::render('reviewer/ethics/dashboard/Index');
    }
}

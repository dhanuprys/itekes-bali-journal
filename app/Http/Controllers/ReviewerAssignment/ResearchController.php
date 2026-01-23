<?php

namespace App\Http\Controllers\ReviewerAssignment;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Inertia\Inertia;

class ResearchController extends Controller
{
    public function index()
    {
        return Inertia::render('reviewer-assignment/research/Index');
    }
}

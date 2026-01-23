<?php

namespace App\Http\Controllers\ReviewerAssignment;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Inertia\Inertia;

class CommunityServiceController extends Controller
{
    public function index()
    {
        return Inertia::render('reviewer-assignment/community-service/Index');
    }
}

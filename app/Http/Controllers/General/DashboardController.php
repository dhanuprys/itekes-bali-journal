<?php

namespace App\Http\Controllers\General;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Inertia\Inertia;

class DashboardController extends Controller
{
    public function index()
    {
        return Inertia::render('general/dashboard/Index');
    }

    public function changelog()
    {
        return Inertia::render('general/dashboard/Changelog');
    }
}

<?php

namespace App\Http\Controllers\Reviewer\Ethics;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Inertia\Inertia;

class OutputController extends Controller
{
    public function index()
    {
        return Inertia::render('reviewer/ethics/output/Index');
    }

    public function show()
    {
        return Inertia::render('reviewer/ethics/output/Show');
    }

    public function comment()
    {

    }

    public function updateDocument()
    {

    }
}

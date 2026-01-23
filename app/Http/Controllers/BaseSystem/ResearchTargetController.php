<?php

namespace App\Http\Controllers\BaseSystem;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Inertia\Inertia;

class ResearchTargetController extends Controller
{
    public function index()
    {
        return Inertia::render('base-system/research-target/Index');
    }

    public function show()
    {
        return Inertia::render('base-system/research-target/Show');
    }

    public function store()
    {

    }

    public function update()
    {

    }

    public function destroy()
    {

    }
}

<?php

namespace App\Http\Controllers\BaseSystem;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Inertia\Inertia;

class StudyProgramController extends Controller
{
    public function index()
    {
        return Inertia::render('base-system/study-program/Index');
    }

    public function show()
    {
        return Inertia::render('base-system/study-program/Show');
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

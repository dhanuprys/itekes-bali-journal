<?php

namespace App\Http\Controllers\BaseSystem;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Inertia\Inertia;

class EthicSubjectController extends Controller
{
    public function index()
    {
        return Inertia::render('base-system/ethic-subject/Index');
    }

    public function show()
    {
        return Inertia::render('base-system/ethic-subject/Show');
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

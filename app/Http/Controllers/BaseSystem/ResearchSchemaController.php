<?php

namespace App\Http\Controllers\BaseSystem;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Inertia\Inertia;

class ResearchSchemaController extends Controller
{
    public function index()
    {
        return Inertia::render('base-system/research-schema/Index');
    }

    public function show()
    {
        return Inertia::render('base-system/research-schema/Show');
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

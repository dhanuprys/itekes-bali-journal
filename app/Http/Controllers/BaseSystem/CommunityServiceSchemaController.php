<?php

namespace App\Http\Controllers\BaseSystem;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Inertia\Inertia;

class CommunityServiceSchemaController extends Controller
{
    public function index()
    {
        return Inertia::render('base-system/community-service-schema/Index');
    }

    public function show()
    {
        return Inertia::render('base-system/community-service-schema/Show');
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

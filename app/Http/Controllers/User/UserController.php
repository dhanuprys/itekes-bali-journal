<?php

namespace App\Http\Controllers\User;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Inertia\Inertia;

class UserController extends Controller
{
    public function index()
    {
        return Inertia::render('user/user/Index');
    }

    public function show()
    {
        return Inertia::render('user/user/Show');
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

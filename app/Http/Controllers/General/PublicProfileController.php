<?php

namespace App\Http\Controllers\General;

use App\Http\Controllers\Controller;
use Inertia\Inertia;

class PublicProfileController extends Controller
{
    public function index($username)
    {
        $user = \App\Models\User::where('username', $username)
            ->select(['name', 'username', 'photo_path', 'created_at', 'id'])
            ->firstOrFail();

        return Inertia::render('general/public-profile/index', [
            'profile' => $user,
        ]);
    }
}

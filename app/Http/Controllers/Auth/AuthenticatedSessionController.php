<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Http\Requests\Auth\LoginRequest;
use App\Models\UserLog;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Str;
use Inertia\Inertia;
use Inertia\Response;

class AuthenticatedSessionController extends Controller
{
    /**
     * Show the login page.
     */
    public function create(Request $request): Response
    {
        $nonce = Str::random(32);
        
        $actions = ['reverse', 'shift', 'xor'];
        $puzzle = [
            'action' => $actions[array_rand($actions)],
            'key' => random_int(1, 99)
        ];
        
        Cache::put('login_nonce_' . $request->session()->getId() . '_' . $nonce, $puzzle, now()->addMinutes(30));

        return Inertia::render('auth/login', [
            'canResetPassword' => Route::has('password.request'),
            'status' => $request->session()->get('status'),
            'loginNonce' => $nonce,
            'loginPuzzle' => base64_encode(json_encode($puzzle)),
        ]);
    }

    /**
     * Handle an incoming authentication request.
     */
    public function store(LoginRequest $request): RedirectResponse
    {
        $request->authenticate();

        $request->session()->regenerate();

        UserLog::create([
            'user_id' => auth()->id(),
            'comment' => 'Berhasil login ke dalam sistem',
        ]);

        return redirect()->intended(route('dashboard', absolute: false));
    }

    /**
     * Destroy an authenticated session.
     */
    public function destroy(Request $request): RedirectResponse
    {
        $userId = auth()->id();
        Auth::guard('web')->logout();

        $request->session()->invalidate();
        $request->session()->regenerateToken();

        if ($userId) {
            UserLog::create([
                'user_id' => $userId,
                'comment' => 'Berhasil logout dari sistem',
            ]);
        }

        return redirect('/');
    }
}

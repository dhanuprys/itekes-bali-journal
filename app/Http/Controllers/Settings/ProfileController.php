<?php

namespace App\Http\Controllers\Settings;

use App\Http\Controllers\Controller;
use App\Http\Requests\Settings\ProfileUpdateRequest;
use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Inertia\Inertia;
use Inertia\Response;

class ProfileController extends Controller
{
    /**
     * Show the user's profile settings page.
     */
    public function edit(Request $request): Response
    {
        return Inertia::render('settings/profile', [
            'mustVerifyEmail' => $request->user() instanceof MustVerifyEmail,
            'status' => $request->session()->get('status'),
        ]);
    }

    /**
     * Update the user's profile information.
     */
    public function update(ProfileUpdateRequest $request, \App\Services\StorageUploadService $uploadService): RedirectResponse
    {
        $request->user()->fill($request->validated());

        if ($request->user()->isDirty('email')) {
            $request->user()->email_verified_at = null;
        }

        if ($request->user()->isDirty('photo_path') && $request->user()->photo_path) {
            $uploadService->markAsUsed($request->user()->photo_path, \App\Enums\StorageUploadAction::USER_PROFILE_PHOTO->name);
        }

        $request->user()->save();

        \App\Models\UserLog::create([
            'user_id' => auth()->id(),
            'comment' => 'Memperbarui profil pengguna',
        ]);

        return to_route('profile.edit');
    }

    /**
     * Delete the user's profile.
     */
    public function destroy(Request $request): RedirectResponse
    {
        $request->validate([
            'password' => ['required', 'current_password'],
        ]);

        $user = $request->user();

        Auth::logout();

        $user->delete();

        \App\Models\UserLog::create([
            'user_id' => $user->id,
            'comment' => 'Menghapus akun pengguna',
        ]);

        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect('/');
    }

    public function loginLogs(Request $request): Response
    {
        $user = $request->user();

        $loginLogs = $user->loginLogs()->latest()->paginate(10);

        return Inertia::render('settings/login-logs', [
            'loginLogs' => $loginLogs,
        ]);
    }
}

<?php

use App\Http\Controllers\General;
use Inertia\Inertia;
use Laravel\Fortify\Features;
use Laravel\Fortify\Http\Controllers\RegisteredUserController;

Route::get('/', function () {
    return Inertia::render('welcome', [
        'canRegister' => Features::enabled(Features::registration()),
    ]);
})->name('home');

// Override Fortify's register route to add rate limiting
if (Features::enabled(Features::registration())) {
    Route::post('/register', [RegisteredUserController::class, 'store'])
        ->middleware(['guest', 'throttle:register']);
}

Route::middleware(['auth'])
    ->group(function () {
        Route::get('dashboard', [General\DashboardController::class, 'index'])->name('dashboard');
        Route::get('notifications', [General\NotificationController::class, 'index'])->name('notifications');
        Route::get('uploaded-files', [General\StorageUploadController::class, 'index'])->name('uploaded-files');

        Route::get('changelog', [General\DashboardController::class, 'changelog'])->name('changelog');
        Route::post('storage-upload', [General\StorageUploadController::class, 'upload'])->name('storage.upload');
    });

// Route::get('u/{username}', [General\PublicProfileController::class, 'index'])->name('public-profile');

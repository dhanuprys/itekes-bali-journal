<?php

use Inertia\Inertia;
use Laravel\Fortify\Features;
use App\Http\Controllers\General;

Route::get('/', function () {
    return Inertia::render('Welcome', [
        'canRegister' => Features::enabled(Features::registration()),
    ]);
})->name('home');

Route::middleware(['auth'])
    ->group(function () {
        Route::get('dashboard', [General\DashboardController::class, 'index'])->name('dashboard');
        Route::get('notifications', [General\NotificationController::class, 'index'])->name('notifications');

        Route::get('changelog', [General\DashboardController::class, 'changelog'])->name('changelog');
        Route::post('storage-upload', [General\StorageUploadController::class, 'upload'])->name('storage.upload');
    });
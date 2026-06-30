<?php

use App\Enums\PermissionRole;
use App\Http\Controllers\User;
use Illuminate\Support\Facades\Route;

Route::middleware(['auth', 'permission:' . PermissionRole::P_MANAGE_USERS->value])
    ->prefix('users')
    ->as('users.users.')
    ->group(
        function () {
            Route::get('', [User\UserController::class, 'index'])->name('index');
            Route::get('{user}', [User\UserController::class, 'show'])->name('show');
            Route::post('', [User\UserController::class, 'store'])->name('store');
            Route::put('{user}', [User\UserController::class, 'update'])->name('update');
            Route::delete('{user}', [User\UserController::class, 'destroy'])->name('destroy');
            Route::post('{user}/impersonate', [User\UserController::class, 'impersonate'])->name('impersonate');
        }
    );

Route::middleware(['auth'])
    ->prefix('users/impersonate')
    ->as('users.users.impersonate.')
    ->group(function () {
        Route::post('leave', [User\UserController::class, 'leaveImpersonate'])->name('leave');
    });

Route::middleware(['auth', 'permission:' . PermissionRole::P_MANAGE_USERS->value])
    ->prefix('roles')
    ->as('users.roles.')
    ->group(
        function () {
            Route::get('', [User\RoleController::class, 'index'])->name('index');
            Route::get('{role}', [User\RoleController::class, 'show'])->name('show');
            Route::post('', [User\RoleController::class, 'store'])->name('store');
            Route::put('{role}', [User\RoleController::class, 'update'])->name('update');
            Route::delete('{role}', [User\RoleController::class, 'destroy'])->name('destroy');
        }
    );

Route::middleware(['auth', 'permission:' . PermissionRole::P_MANAGE_USERS->value])
    ->prefix('permissions')
    ->as('users.permissions.')
    ->group(
        function () {
            Route::get('', [User\PermissionController::class, 'index'])->name('index');
            Route::get('{permission}', [User\PermissionController::class, 'show'])->name('show');
        }
    );

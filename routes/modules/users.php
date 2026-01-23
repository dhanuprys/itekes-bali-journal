<?php

use App\Enums\PermissionRole;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\User;

Route::middleware(['auth', 'permission:' . PermissionRole::P_MANAGE_USERS->value])
    ->prefix('users')
    ->as('users.users.')
    ->group(
        function () {
            Route::get('', [User\UserController::class, 'index'])->name('index');
            Route::get('{id}', [User\UserController::class, 'show'])->name('show');
            Route::post('', [User\UserController::class, 'store'])->name('store');
            Route::put('{id}', [User\UserController::class, 'update'])->name('update');
            Route::delete('{id}', [User\UserController::class, 'destroy'])->name('destroy');
        }
    );

Route::middleware(['auth', 'permission:' . PermissionRole::P_MANAGE_USERS->value])
    ->prefix('roles')
    ->as('users.roles.')
    ->group(
        function () {
            Route::get('', [User\RoleController::class, 'index'])->name('index');
            Route::get('{id}', [User\RoleController::class, 'show'])->name('show');
            Route::post('', [User\RoleController::class, 'store'])->name('store');
            Route::put('{id}', [User\RoleController::class, 'update'])->name('update');
            Route::delete('{id}', [User\RoleController::class, 'destroy'])->name('destroy');
        }
    );

Route::middleware(['auth', 'permission:' . PermissionRole::P_MANAGE_USERS->value])
    ->prefix('permissions')
    ->as('users.permissions.')
    ->group(
        function () {
            Route::get('', [User\PermissionController::class, 'index'])->name('index');
        }
    );

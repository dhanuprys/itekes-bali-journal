<?php

use App\Enums\PermissionRole;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\ReviewerAssignment;

Route::middleware(['auth'])
    ->prefix('assign-reviewer')
    ->as('reviewer_assignment.')
    ->group(
        function () {
            Route::middleware(['permission:' . PermissionRole::P_ASSIGN_REVIEWER_RESEARCH->value])
                ->prefix('research')
                ->as('research.')
                ->group(
                    function () {
                        Route::get('', [ReviewerAssignment\ResearchController::class, 'index'])->name('index');
                        Route::post('{id}/assign', [ReviewerAssignment\ResearchController::class, 'store'])->name('store');
                    }
                );

            Route::middleware(['permission:' . PermissionRole::P_ASSIGN_REVIEWER_COMMUNITY_SERVICE->value])
                ->prefix('community-service')
                ->as('community_service.')
                ->group(
                    function () {
                        Route::get('', [ReviewerAssignment\CommunityServiceController::class, 'index'])->name('index');
                        Route::post('{id}/assign', [ReviewerAssignment\CommunityServiceController::class, 'store'])->name('store');
                    }
                );
        }
    );
<?php

use App\Enums\PermissionRole;
use App\Http\Controllers\BaseSystem\CommunityServiceSchemaController;
use App\Http\Controllers\BaseSystem\CommunityServiceTargetController;
use App\Http\Controllers\BaseSystem\EthicSubjectController;
use App\Http\Controllers\BaseSystem\ResearchSchemaController;
use App\Http\Controllers\BaseSystem\ResearchTargetController;
use App\Http\Controllers\BaseSystem\StudyProgramController;
use App\Http\Controllers\BaseSystem\UserLogController;
use Illuminate\Support\Facades\Route;

Route::middleware(['auth', 'permission:' . PermissionRole::P_MANAGE_FORM->value])
    ->prefix('master')
    ->as('base_system.master.')
    ->group(
        function () {
            Route::group(
                ['prefix' => 'research-target', 'as' => 'research_target.'],
                function () {
                    Route::get('', [ResearchTargetController::class, 'index'])->name('index');
                    Route::get('{id}', [ResearchTargetController::class, 'show'])->name('show');
                    Route::post('', [ResearchTargetController::class, 'store'])->name('store');
                    Route::put('{id}', [ResearchTargetController::class, 'update'])->name('update');
                    Route::delete('{id}', [ResearchTargetController::class, 'destroy'])->name('destroy');
                }
            );

            Route::group(
                ['prefix' => 'research-schema', 'as' => 'research_schema.'],
                function () {
                    Route::get('', [ResearchSchemaController::class, 'index'])->name('index');
                    Route::get('{id}', [ResearchSchemaController::class, 'show'])->name('show');
                    Route::post('', [ResearchSchemaController::class, 'store'])->name('store');
                    Route::put('{id}', [ResearchSchemaController::class, 'update'])->name('update');
                    Route::delete('{id}', [ResearchSchemaController::class, 'destroy'])->name('destroy');
                }
            );

            Route::group(
                ['prefix' => 'community-service-target', 'as' => 'community_service_target.'],
                function () {
                    Route::get('', [CommunityServiceTargetController::class, 'index'])->name('index');
                    Route::get('{id}', [CommunityServiceTargetController::class, 'show'])->name('show');
                    Route::post('', [CommunityServiceTargetController::class, 'store'])->name('store');
                    Route::put('{id}', [CommunityServiceTargetController::class, 'update'])->name('update');
                    Route::delete('{id}', [CommunityServiceTargetController::class, 'destroy'])->name('destroy');
                }
            );

            Route::group(
                ['prefix' => 'community-service-schema', 'as' => 'community_service_schema.'],
                function () {
                    Route::get('', [CommunityServiceSchemaController::class, 'index'])->name('index');
                    Route::get('{id}', [CommunityServiceSchemaController::class, 'show'])->name('show');
                    Route::post('', [CommunityServiceSchemaController::class, 'store'])->name('store');
                    Route::put('{id}', [CommunityServiceSchemaController::class, 'update'])->name('update');
                    Route::delete('{id}', [CommunityServiceSchemaController::class, 'destroy'])->name('destroy');
                }
            );

            Route::group(
                ['prefix' => 'ethic-subject', 'as' => 'ethic_subject.'],
                function () {
                    Route::get('', [EthicSubjectController::class, 'index'])->name('index');
                    Route::get('{id}', [EthicSubjectController::class, 'show'])->name('show');
                    Route::post('', [EthicSubjectController::class, 'store'])->name('store');
                    Route::put('{id}', [EthicSubjectController::class, 'update'])->name('update');
                    Route::delete('{id}', [EthicSubjectController::class, 'destroy'])->name('destroy');
                }
            );
        }
    );

Route::middleware(['auth', 'permission:' . PermissionRole::P_MANAGE_BASE->value])
    ->prefix('study-program')
    ->as('base_system.study_program.')
    ->group(
        function () {
            Route::get('', [StudyProgramController::class, 'index'])->name('index');
            Route::get('{id}', [StudyProgramController::class, 'show'])->name('show');
            Route::post('', [StudyProgramController::class, 'store'])->name('store');
            Route::put('{id}', [StudyProgramController::class, 'update'])->name('update');
            Route::delete('{id}', [StudyProgramController::class, 'destroy'])->name('destroy');
        }
    );

Route::middleware(['auth', 'permission:' . PermissionRole::P_VIEW_USER_LOGS->value])
    ->prefix('user-logs')
    ->as('base_system.user_logs.')
    ->group(
        function () {
            Route::get('', [UserLogController::class, 'index'])->name('index');
        }
    );
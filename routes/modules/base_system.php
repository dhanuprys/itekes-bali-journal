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
                    Route::get('{researchTarget}', [ResearchTargetController::class, 'show'])->name('show');
                    Route::post('', [ResearchTargetController::class, 'store'])->name('store');
                    Route::put('{researchTarget}', [ResearchTargetController::class, 'update'])->name('update');
                    Route::delete('{researchTarget}', [ResearchTargetController::class, 'destroy'])->name('destroy');
                }
            );

            Route::group(
                ['prefix' => 'research-schema', 'as' => 'research_schema.'],
                function () {
                    Route::get('', [ResearchSchemaController::class, 'index'])->name('index');
                    Route::get('{researchSchema}', [ResearchSchemaController::class, 'show'])->name('show');
                    Route::post('', [ResearchSchemaController::class, 'store'])->name('store');
                    Route::put('{researchSchema}', [ResearchSchemaController::class, 'update'])->name('update');
                    Route::delete('{researchSchema}', [ResearchSchemaController::class, 'destroy'])->name('destroy');
                }
            );

            Route::group(
                ['prefix' => 'community-service-target', 'as' => 'community_service_target.'],
                function () {
                    Route::get('', [CommunityServiceTargetController::class, 'index'])->name('index');
                    Route::get('{communityServiceTarget}', [CommunityServiceTargetController::class, 'show'])->name('show');
                    Route::post('', [CommunityServiceTargetController::class, 'store'])->name('store');
                    Route::put('{communityServiceTarget}', [CommunityServiceTargetController::class, 'update'])->name('update');
                    Route::delete('{communityServiceTarget}', [CommunityServiceTargetController::class, 'destroy'])->name('destroy');
                }
            );

            Route::group(
                ['prefix' => 'community-service-schema', 'as' => 'community_service_schema.'],
                function () {
                    Route::get('', [CommunityServiceSchemaController::class, 'index'])->name('index');
                    Route::get('{communityServiceSchema}', [CommunityServiceSchemaController::class, 'show'])->name('show');
                    Route::post('', [CommunityServiceSchemaController::class, 'store'])->name('store');
                    Route::put('{communityServiceSchema}', [CommunityServiceSchemaController::class, 'update'])->name('update');
                    Route::delete('{communityServiceSchema}', [CommunityServiceSchemaController::class, 'destroy'])->name('destroy');
                }
            );

            Route::group(
                ['prefix' => 'ethic-subject', 'as' => 'ethic_subject.'],
                function () {
                    Route::get('', [EthicSubjectController::class, 'index'])->name('index');
                    Route::get('{ethicalClearanceSubject}', [EthicSubjectController::class, 'show'])->name('show');
                    Route::post('', [EthicSubjectController::class, 'store'])->name('store');
                    Route::put('{ethicalClearanceSubject}', [EthicSubjectController::class, 'update'])->name('update');
                    Route::delete('{ethicalClearanceSubject}', [EthicSubjectController::class, 'destroy'])->name('destroy');
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
            Route::get('{studyProgram}', [StudyProgramController::class, 'show'])->name('show');
            Route::post('', [StudyProgramController::class, 'store'])->name('store');
            Route::put('{studyProgram}', [StudyProgramController::class, 'update'])->name('update');
            Route::delete('{studyProgram}', [StudyProgramController::class, 'destroy'])->name('destroy');
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

<?php

use App\Enums\PermissionRole;
use App\Http\Controllers\ReviewRequest;
use Illuminate\Support\Facades\Route;

Route::middleware(['auth', 'permission:' . PermissionRole::P_REQUEST_RESEARCH_REVIEW->value])
    ->prefix('apply/research')
    ->as('apply.research.')
    ->group(
        function () {
            Route::get('', [ReviewRequest\Research\DashboardController::class, 'index'])->name('index');
            Route::get('{id}/revisions', [ReviewRequest\Research\DashboardController::class, 'revisions'])->name('revisions');
            Route::get('{id}/revisions/{revision_id}', [ReviewRequest\Research\DashboardController::class, 'showRevision'])->name('revision');

            Route::prefix('proposal')->as('proposal.')->group(
                function () {
                    Route::get('', [ReviewRequest\Research\ProposalController::class, 'index'])->name('index');
                    Route::get('create', [ReviewRequest\Research\ProposalController::class, 'create'])->name('create');
                    Route::post('', [ReviewRequest\Research\ProposalController::class, 'store'])->name('store');
                    Route::get('{id}', [ReviewRequest\Research\ProposalController::class, 'show'])->name('show');
                    Route::get('{id}/edit', [ReviewRequest\Research\ProposalController::class, 'edit'])->name('edit');
                    Route::post('{id}/revise', [ReviewRequest\Research\ProposalController::class, 'revise'])->name('revise');

                    Route::post('{id}/comment', [ReviewRequest\Research\ProposalController::class, 'comment'])->name('comment');
                }
            );

            Route::prefix('progress-report')->as('progress_report.')->group(
                function () {
                    Route::get('', [ReviewRequest\Research\ProgressReportController::class, 'index'])->name('index');
                    Route::get('create', [ReviewRequest\Research\ProgressReportController::class, 'create'])->name('create');
                    Route::post('', [ReviewRequest\Research\ProgressReportController::class, 'store'])->name('store');
                    Route::get('{id}', [ReviewRequest\Research\ProgressReportController::class, 'show'])->name('show');
                    Route::get('{id}/edit', [ReviewRequest\Research\ProgressReportController::class, 'edit'])->name('edit');
                    Route::post('{id}/revise', [ReviewRequest\Research\ProgressReportController::class, 'revise'])->name('revise');

                    Route::post('{id}/comment', [ReviewRequest\Research\ProgressReportController::class, 'comment'])->name('comment');
                }
            );

            Route::prefix('final-report')->as('final_report.')->group(
                function () {
                    Route::get('', [ReviewRequest\Research\FinalReportController::class, 'index'])->name('index');
                    Route::get('create', [ReviewRequest\Research\FinalReportController::class, 'create'])->name('create');
                    Route::post('', [ReviewRequest\Research\FinalReportController::class, 'store'])->name('store');
                    Route::get('{id}', [ReviewRequest\Research\FinalReportController::class, 'show'])->name('show');
                    Route::get('{id}/edit', [ReviewRequest\Research\FinalReportController::class, 'edit'])->name('edit');
                    Route::post('{id}/revise', [ReviewRequest\Research\FinalReportController::class, 'revise'])->name('revise');
                }
            );
        }
    );

Route::middleware(['auth', 'permission:' . PermissionRole::P_REQUEST_COMMUNITY_SERVICE_REVIEW->value])
    ->prefix('apply/community-service')
    ->as('apply.community_service.')
    ->group(
        function () {
            Route::get('', [ReviewRequest\CommunityService\DashboardController::class, 'index'])->name('index');
            Route::get('{id}/revisions', [ReviewRequest\CommunityService\DashboardController::class, 'revisions'])->name('revisions');
            Route::get('{id}/revisions/{revision_id}', [ReviewRequest\CommunityService\DashboardController::class, 'showRevision'])->name('revision');

            Route::prefix('proposal')->as('proposal.')->group(
                function () {
                    Route::get('', [ReviewRequest\CommunityService\ProposalController::class, 'index'])->name('index');
                    Route::get('create', [ReviewRequest\CommunityService\ProposalController::class, 'create'])->name('create');
                    Route::post('', [ReviewRequest\CommunityService\ProposalController::class, 'store'])->name('store');
                    Route::get('{id}', [ReviewRequest\CommunityService\ProposalController::class, 'show'])->name('show');
                    Route::get('{id}/edit', [ReviewRequest\CommunityService\ProposalController::class, 'edit'])->name('edit');
                    Route::post('{id}/revise', [ReviewRequest\CommunityService\ProposalController::class, 'revise'])->name('revise');

                    Route::post('{id}/comment', [ReviewRequest\CommunityService\ProposalController::class, 'comment'])->name('comment');
                }
            );

            Route::prefix('progress-report')->as('progress_report.')->group(
                function () {
                    Route::get('', [ReviewRequest\CommunityService\ProgressReportController::class, 'index'])->name('index');
                    Route::get('create', [ReviewRequest\CommunityService\ProgressReportController::class, 'create'])->name('create');
                    Route::post('', [ReviewRequest\CommunityService\ProgressReportController::class, 'store'])->name('store');
                    Route::get('{id}', [ReviewRequest\CommunityService\ProgressReportController::class, 'show'])->name('show');
                    Route::get('{id}/edit', [ReviewRequest\CommunityService\ProgressReportController::class, 'edit'])->name('edit');
                    Route::post('{id}/revise', [ReviewRequest\CommunityService\ProgressReportController::class, 'revise'])->name('revise');

                    Route::post('{id}/comment', [ReviewRequest\CommunityService\ProgressReportController::class, 'comment'])->name('comment');
                }
            );

            Route::prefix('final-report')->as('final_report.')->group(
                function () {
                    Route::get('', [ReviewRequest\CommunityService\FinalReportController::class, 'index'])->name('index');
                    Route::get('create', [ReviewRequest\CommunityService\FinalReportController::class, 'create'])->name('create');
                    Route::post('', [ReviewRequest\CommunityService\FinalReportController::class, 'store'])->name('store');
                    Route::get('{id}', [ReviewRequest\CommunityService\FinalReportController::class, 'show'])->name('show');
                    Route::get('{id}/edit', [ReviewRequest\CommunityService\FinalReportController::class, 'edit'])->name('edit');
                    Route::post('{id}/revise', [ReviewRequest\CommunityService\FinalReportController::class, 'revise'])->name('revise');
                }
            );
        }
    );

Route::middleware(['auth', 'permission:' . PermissionRole::P_REQUEST_ETHICS_REVIEW->value])
    ->prefix('apply/ethics')
    ->as('apply.ethics.')
    ->group(
        function () {
            Route::get('', [ReviewRequest\Ethics\DashboardController::class, 'index'])->name('index');
            Route::get('{id}/revisions', [ReviewRequest\Ethics\DashboardController::class, 'revisions'])->name('revisions');
            Route::get('{id}/revisions/{revision_id}', [ReviewRequest\Ethics\DashboardController::class, 'showRevision'])->name('revision');

            Route::prefix('proposal')->as('proposal.')->group(
                function () {
                    Route::get('', [ReviewRequest\Ethics\ProposalController::class, 'index'])->name('index');
                    Route::get('create', [ReviewRequest\Ethics\ProposalController::class, 'create'])->name('create');
                    Route::post('', [ReviewRequest\Ethics\ProposalController::class, 'store'])->name('store');
                    Route::get('{id}', [ReviewRequest\Ethics\ProposalController::class, 'show'])->name('show');
                    Route::get('{id}/edit', [ReviewRequest\Ethics\ProposalController::class, 'edit'])->name('edit');

                    // update when rejected by reviewer
                    Route::post('{id}/revise', [ReviewRequest\Ethics\ProposalController::class, 'revise'])->name('revise');
                    Route::post('{id}/comment', [ReviewRequest\Ethics\ProposalController::class, 'comment'])->name('comment');
                }
            );

            Route::prefix('wait-for-output')->as('wait_for_output.')->group(
                function () {
                    Route::get('', [ReviewRequest\Ethics\OutputController::class, 'waitForOutput'])->name('index');
                    Route::get('{id}', [ReviewRequest\Ethics\OutputController::class, 'waitForOutputDetail'])->name('show');
                }
            );

            Route::prefix('output')->as('output.')->group(
                function () {
                    Route::get('', [ReviewRequest\Ethics\OutputController::class, 'index'])->name('index');
                    Route::get('{id}', [ReviewRequest\Ethics\OutputController::class, 'show'])->name('show');
                }
            );
        }
    );

<?php

use App\Enums\PermissionRole;
use App\Http\Controllers\Reviewer;
use Illuminate\Support\Facades\Route;

Route::middleware(['auth', 'permission:' . PermissionRole::P_REVIEW_RESEARCH->value])
    ->prefix('review/research')
    ->as('review.research.')
    ->group(
        function () {
            Route::get('', [Reviewer\Research\DashboardController::class, 'index'])->name('index');

            Route::prefix('proposal')->as('proposal.')->group(
                function () {
                    Route::get('', [Reviewer\Research\ProposalController::class, 'index'])->name('index');
                    Route::get('{id}', [Reviewer\Research\ProposalController::class, 'show'])->name('show');

                    Route::post('{id}/comment', [Reviewer\Research\ProposalController::class, 'comment'])->name('comment');
                    Route::post('{id}/change-state', [Reviewer\Research\ProposalController::class, 'changeState'])->name('change-state');
                }
            );

            Route::prefix('progress-report')->as('progress_report.')->group(
                function () {
                    Route::get('', [Reviewer\Research\ProgressReportController::class, 'index'])->name('index');
                    Route::get('{id}', [Reviewer\Research\ProgressReportController::class, 'show'])->name('show');

                    Route::post('{id}/comment', [Reviewer\Research\ProgressReportController::class, 'comment'])->name('comment');
                    Route::post('{id}/change-state', [Reviewer\Research\ProgressReportController::class, 'changeState'])->name('change-state');
                }
            );

            Route::prefix('final-report')->as('final_report.')->group(
                function () {
                    Route::get('', [Reviewer\Research\FinalReportController::class, 'index'])->name('index');
                    Route::get('{id}', [Reviewer\Research\FinalReportController::class, 'show'])->name('show');

                    Route::post('{id}/comment', [Reviewer\Research\FinalReportController::class, 'comment'])->name('comment');
                    Route::post('{id}/change-state', [Reviewer\Research\FinalReportController::class, 'changeState'])->name('change-state');
                }
            );
        }
    );

Route::middleware(['auth', 'permission:' . PermissionRole::P_REVIEW_COMMUNITY_SERVICE->value])
    ->prefix('review/community-service')
    ->as('review.community_service.')
    ->group(
        function () {
            Route::get('', [Reviewer\CommunityService\DashboardController::class, 'index'])->name('index');

            Route::prefix('proposal')->as('proposal.')->group(
                function () {
                    Route::get('', [Reviewer\CommunityService\ProposalController::class, 'index'])->name('index');
                    Route::get('{id}', [Reviewer\CommunityService\ProposalController::class, 'show'])->name('show');

                    Route::post('{id}/comment', [Reviewer\CommunityService\ProposalController::class, 'comment'])->name('comment');
                    Route::post('{id}/change-state', [Reviewer\CommunityService\ProposalController::class, 'changeState'])->name('change-state');
                }
            );

            Route::prefix('progress-report')->as('progress_report.')->group(
                function () {
                    Route::get('', [Reviewer\CommunityService\ProgressReportController::class, 'index'])->name('index');
                    Route::get('{id}', [Reviewer\CommunityService\ProgressReportController::class, 'show'])->name('show');

                    Route::post('{id}/comment', [Reviewer\CommunityService\ProgressReportController::class, 'comment'])->name('comment');
                    Route::post('{id}/change-state', [Reviewer\CommunityService\ProgressReportController::class, 'changeState'])->name('change-state');
                }
            );

            Route::prefix('final-report')->as('final_report.')->group(
                function () {
                    Route::get('', [Reviewer\CommunityService\FinalReportController::class, 'index'])->name('index');
                    Route::get('{id}', [Reviewer\CommunityService\FinalReportController::class, 'show'])->name('show');

                    Route::post('{id}/comment', [Reviewer\CommunityService\FinalReportController::class, 'comment'])->name('comment');
                    Route::post('{id}/change-state', [Reviewer\CommunityService\FinalReportController::class, 'changeState'])->name('change-state');
                }
            );
        }
    );

Route::middleware(['auth', 'permission:' . PermissionRole::P_REVIEW_ETHICS->value])
    ->prefix('review/ethics')
    ->as('review.ethics.')
    ->group(
        function () {
            Route::get('', [Reviewer\Ethics\DashboardController::class, 'index'])->name('index');

            Route::prefix('proposal')->as('proposal.')->group(
                function () {
                    Route::get('', [Reviewer\Ethics\ProposalController::class, 'index'])->name('index');
                    Route::get('{id}', [Reviewer\Ethics\ProposalController::class, 'show'])->name('show');

                    Route::post('{id}/comment', [Reviewer\Ethics\ProposalController::class, 'comment'])->name('comment');
                    Route::post('{id}/change-state', [Reviewer\Ethics\ProposalController::class, 'changeState'])->name('change-state');
                }
            );

            Route::prefix('wait-for-output')->as('wait_for_output.')->group(
                function () {
                    Route::get('', [Reviewer\Ethics\OutputController::class, 'waitForOutput'])->name('index');
                    Route::get('{id}', [Reviewer\Ethics\OutputController::class, 'show'])->name('show');

                    Route::post('{id}/comment', [Reviewer\Ethics\OutputController::class, 'comment'])->name('comment');
                    Route::post('{id}/document', [Reviewer\Ethics\OutputController::class, 'updateDocument'])->name('update_document');
                }
            );

            Route::prefix('output')->as('output.')->group(
                function () {
                    Route::get('', [Reviewer\Ethics\OutputController::class, 'index'])->name('index');
                    Route::get('{id}', [Reviewer\Ethics\OutputController::class, 'show'])->name('show');
                    Route::post('{id}/document', [Reviewer\Ethics\OutputController::class, 'updateDocument'])->name('update_document');
                }
            );

            Route::prefix('verification')->as('verification.')->group(
                function () {
                    Route::get('', [Reviewer\Ethics\OutputController::class, 'verificationIndex'])->name('index');
                    Route::get('{id}', [Reviewer\Ethics\OutputController::class, 'verificationShow'])->name('show');
                    Route::post('{id}/verify', [Reviewer\Ethics\OutputController::class, 'verify'])->name('verify');
                }
            );
        }
    );

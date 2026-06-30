<?php

namespace App\Http\Controllers\Reviewer\Ethics;

use App\Enums\EthicsReviewStage;
use App\Http\Controllers\Controller;
use App\Models\EthicalClearanceSubmission;
use Inertia\Inertia;

class DashboardController extends Controller
{
    public function index()
    {
        $userId = \Illuminate\Support\Facades\Auth::id();
        
        $proposalCount = EthicalClearanceSubmission::whereHas('reviewers', function ($query) use ($userId) {
                $query->where('user_id', $userId);
            })
            ->where('stage', EthicsReviewStage::PROPOSAL->value)
            ->count();
            
        $waitForOutputCount = EthicalClearanceSubmission::where(function ($query) {
                $query->where('stage', EthicsReviewStage::OUTPUT->value)
                    ->orWhere(function ($q) {
                        $q->where('stage', EthicsReviewStage::VERIFICATION->value)
                            ->where('status', '!=', \App\Enums\EthicsStatus::APPROVED->value);
                    });
            })->count();
            
        // Assuming output completed is when it's fully verified (stage = VERIFICATION and status = APPROVED) or just count anything that is completed
        $outputCompletedCount = EthicalClearanceSubmission::where('stage', EthicsReviewStage::VERIFICATION->value)
            ->where('status', \App\Enums\EthicsStatus::APPROVED->value)
            ->count();

        $verificationCount = EthicalClearanceSubmission::whereHas('reviewers', function ($query) use ($userId) {
                $query->where('user_id', $userId);
            })
            ->where('stage', EthicsReviewStage::VERIFICATION->value)
            ->where('status', '!=', \App\Enums\EthicsStatus::APPROVED->value)
            ->count();

        return Inertia::render('reviewer/ethics/dashboard/index', [
            'proposalCount' => $proposalCount,
            'waitForOutputCount' => $waitForOutputCount,
            'outputCompletedCount' => $outputCompletedCount,
            'verificationCount' => $verificationCount,
        ]);
    }
}

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
        $proposalCount = EthicalClearanceSubmission::where('stage', EthicsReviewStage::PROPOSAL->value)->count();
        $waitForOutputCount = EthicalClearanceSubmission::where('stage', EthicsReviewStage::OUTPUT->value)
            ->whereDoesntHave('outputs', function ($q) {
                $q->whereNotNull('document_path');
            })->count();
            
        $outputCompletedCount = EthicalClearanceSubmission::where('stage', EthicsReviewStage::OUTPUT->value)
            ->whereHas('outputs', function ($q) {
                $q->whereNotNull('document_path');
            })->count();

        return Inertia::render('reviewer/ethics/dashboard/Index', [
            'proposalCount' => $proposalCount,
            'waitForOutputCount' => $waitForOutputCount,
            'outputCompletedCount' => $outputCompletedCount,
        ]);
    }
}

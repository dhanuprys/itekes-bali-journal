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
        $outputCount = EthicalClearanceSubmission::where('stage', EthicsReviewStage::OUTPUT->value)->count();

        return Inertia::render('reviewer/ethics/dashboard/Index', [
            'proposalCount' => $proposalCount,
            'outputCount' => $outputCount,
        ]);
    }
}

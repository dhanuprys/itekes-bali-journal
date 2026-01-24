<?php

namespace App\Enums;

enum CommunityServiceReviewStage: string
{
    case PROPOSAL = 'proposal';
    case PROGRESS_REPORT = 'progress_report';
    case FINAL_REPORT = 'final_report';
}

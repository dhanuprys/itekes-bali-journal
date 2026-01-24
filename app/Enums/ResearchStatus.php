<?php

namespace App\Enums;

enum ResearchStatus: string
{
    case NEED_REVIEW = 'need_review';
    case IN_REVIEW = 'in_review';
    case REVISION_NEEDED = 'revision_needed';
    case APPROVED = 'approved';
    case REJECTED = 'rejected';
    case CANCELED = 'canceled';
}

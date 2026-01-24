<?php

namespace App\Enums;

enum CommunityServiceStatus: string
{
    case NEED_REVIEW = 'need_review';
    case IN_REVIEW = 'in_review';
    case REVISION_NEEDED = 'revision_needed';
    case APPROVED = 'approved';
    case REJECTED = 'rejected';
}

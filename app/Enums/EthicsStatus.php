<?php

namespace App\Enums;

enum EthicsStatus: string
{
    case NEED_REVIEW = 'need_review';
    case REVISION_NEEDED = 'revision_needed';
    case APPROVED = 'approved';
    case REJECTED = 'rejected';
    case CANCELED = 'canceled';
}

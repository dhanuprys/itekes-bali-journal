<?php

namespace App\Enums;

enum EthicsReviewStage: string
{
    case PROPOSAL = 'proposal';
    case OUTPUT = 'output';
    case VERIFICATION = 'verification';
}

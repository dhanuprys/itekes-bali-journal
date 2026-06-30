<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class EthicalClearanceProposalReview extends Model
{
    protected $fillable = [
        'ethical_clearance_submission_id',
        'user_id',
        'status',
        'notes',
    ];

    public function submission(): BelongsTo
    {
        return $this->belongsTo(EthicalClearanceSubmission::class, 'ethical_clearance_submission_id');
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }
}

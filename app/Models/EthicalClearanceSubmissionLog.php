<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class EthicalClearanceSubmissionLog extends Model
{
    use HasFactory;

    protected $fillable = ['user_id', 'ethical_clearance_submission_id', 'old_status', 'new_status', 'comment'];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function submission(): BelongsTo
    {
        return $this->belongsTo(EthicalClearanceSubmission::class, 'ethical_clearance_submission_id');
    }
}

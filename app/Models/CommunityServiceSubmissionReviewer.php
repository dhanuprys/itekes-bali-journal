<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class CommunityServiceSubmissionReviewer extends Model
{
    use HasFactory;

    protected $fillable = ['user_id', 'community_service_submission_id'];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function submission(): BelongsTo
    {
        return $this->belongsTo(CommunityServiceSubmission::class, 'community_service_submission_id');
    }
}

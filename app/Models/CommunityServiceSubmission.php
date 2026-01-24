<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class CommunityServiceSubmission extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'user_id',
        'stage',
        'status',
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function details(): HasMany
    {
        return $this->hasMany(CommunityServiceSubmissionDetail::class);
    }

    public function reviewers(): HasMany
    {
        return $this->hasMany(CommunityServiceSubmissionReviewer::class);
    }

    public function latestDetail()
    {
        return $this->hasOne(CommunityServiceSubmissionDetail::class)->latestOfMany();
    }
}

<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Database\Eloquent\SoftDeletes;

class CommunityServiceSubmission extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = ['user_id', 'stage', 'status'];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function detail(): HasOne
    {
        return $this->hasOne(CommunityServiceSubmissionDetail::class);
    }

    public function reviewers(): HasMany
    {
        return $this->hasMany(CommunityServiceSubmissionReviewer::class);
    }

    public function logs(): HasMany
    {
        return $this->hasMany(CommunityServiceSubmissionLog::class);
    }
}

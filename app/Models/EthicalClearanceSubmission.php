<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Database\Eloquent\SoftDeletes;

class EthicalClearanceSubmission extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = ['user_id', 'category', 'stage', 'status'];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function details(): HasMany
    {
        return $this->hasMany(EthicalClearanceDetail::class);
    }

    public function latestDetail()
    {
        return $this->hasOne(EthicalClearanceDetail::class)->latestOfMany();
    }

    public function detail(): HasOne
    {
        return $this->hasOne(EthicalClearanceDetail::class);
    }

    public function reviewers(): HasMany
    {
        return $this->hasMany(EthicalClearanceSubmissionReviewer::class);
    }

    public function logs(): HasMany
    {
        return $this->hasMany(EthicalClearanceSubmissionLog::class);
    }

    public function outputs(): HasMany
    {
        return $this->hasMany(EthicalClearanceOutput::class);
    }

    public function latestOutput()
    {
        return $this->hasOne(EthicalClearanceOutput::class)->latestOfMany();
    }
}

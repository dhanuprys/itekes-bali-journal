<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class EthicalClearanceDetail extends Model
{
    use HasFactory;

    protected $fillable = [
        'ethical_clearance_submission_id',
    ];

    public function submission(): BelongsTo
    {
        return $this->belongsTo(EthicalClearanceSubmission::class, 'ethical_clearance_submission_id');
    }

    public function files(): HasMany
    {
        return $this->hasMany(EthicalClearanceDetailFile::class, 'ethical_clearance_detail_id');
    }

    public function reviewers(): HasMany
    {
        return $this->hasMany(EthicalClearanceSubdetailReviewer::class, 'ethical_clearance_subdetail_id');
    }

    public function members(): HasMany
    {
        return $this->hasMany(EthicalClearanceMember::class, 'ethical_clearance_subdetail_id');
    }

    public function comments(): HasMany
    {
        return $this->hasMany(EthicalClearanceComment::class, 'ethical_clearance_subdetail_id');
    }
}

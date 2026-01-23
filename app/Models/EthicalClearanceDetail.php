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
        'is_multicenter',
        'research_title',
        'leader_name',
        'research_location',
        'institution_details',
        'ethical_clearance_subject_id',
        'duration_per_participant',
        'proposal_summary',
        'ethical_issues',
        'ethical_mitigation',
        'experimental_procedure',
        'potential_hazards',
        'previous_experience',
        'documentation_method'
    ];

    public function submission(): BelongsTo
    {
        return $this->belongsTo(EthicalClearanceSubmission::class, 'ethical_clearance_submission_id');
    }

    public function subject(): BelongsTo
    {
        return $this->belongsTo(EthicalClearanceSubject::class, 'ethical_clearance_subject_id');
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

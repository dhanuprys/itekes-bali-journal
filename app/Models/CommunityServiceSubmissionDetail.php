<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class CommunityServiceSubmissionDetail extends Model
{
    use HasFactory;

    protected $fillable = [
        // proposal (locked when already on progress-report)
        'community_service_submission_id',
        'leader_name',
        'study_program_id',
        'title',
        'budget',
        'community_service_target_id',
        'proposal_path',
        // it has members (and still editable even on progress-report)

        // progress-report / final-report
        'community_service_schema_id',
        'final_leader_name',
        'leader_nidn',
        'final_title',
        'final_report_path',
        'manuscript_path'
    ];

    public function submission(): BelongsTo
    {
        return $this->belongsTo(CommunityServiceSubmission::class, 'community_service_submission_id');
    }

    public function studyProgram(): BelongsTo
    {
        return $this->belongsTo(StudyProgram::class);
    }

    public function target(): BelongsTo
    {
        return $this->belongsTo(CommunityServiceTarget::class, 'community_service_target_id');
    }

    public function schema(): BelongsTo
    {
        return $this->belongsTo(CommunityServiceSchema::class, 'community_service_schema_id');
    }

    public function reviewers(): HasMany
    {
        return $this->hasMany(CommunityServiceSubdetailReviewer::class, 'community_service_subdetail_id');
    }

    public function members(): HasMany
    {
        return $this->hasMany(CommunityServiceMember::class, 'community_service_subdetail_id');
    }

    public function comments(): HasMany
    {
        return $this->hasMany(CommunityServiceSubmissionComment::class, 'community_service_subdetail_id');
    }
}

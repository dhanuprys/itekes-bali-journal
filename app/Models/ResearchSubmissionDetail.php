<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class ResearchSubmissionDetail extends Model
{
    use HasFactory;

    protected $fillable = [
        // proposal
        'research_submission_id',
        'leader_name',
        'study_program_id',
        'title',
        'budget',
        'research_target_id',
        'proposal_path',
        // it has members (and still editable even on progress-report)

        // progress-report / final-report
        'research_schema_id',
        'final_leader_name',
        'leader_nidn',
        'leader_nuptk',
        'final_title',
        'progress_report_path',
        'final_report_path',
        'manuscript_path',
        'supplementary_path',
        'notes'
    ];

    public function submission(): BelongsTo
    {
        return $this->belongsTo(ResearchSubmission::class, 'research_submission_id');
    }

    public function studyProgram(): BelongsTo
    {
        return $this->belongsTo(StudyProgram::class);
    }

    public function target(): BelongsTo
    {
        return $this->belongsTo(ResearchTarget::class, 'research_target_id');
    }

    public function schema(): BelongsTo
    {
        return $this->belongsTo(ResearchSchema::class, 'research_schema_id');
    }

    public function reviewers(): HasMany
    {
        return $this->hasMany(ResearchSubdetailReviewer::class, 'research_submission_detail_id');
    }

    public function members(): HasMany
    {
        return $this->hasMany(ResearchMember::class, 'research_subdetail_id');
    }

    public function comments(): HasMany
    {
        return $this->hasMany(ResearchSubmissionComment::class, 'research_subdetail_id');
    }
}

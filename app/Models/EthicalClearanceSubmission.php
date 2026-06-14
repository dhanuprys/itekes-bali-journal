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

    protected $fillable = ['user_id', 'category', 'stage', 'status', 'is_student', 'student_nim', 'study_program_id', 'wali_name', 'payment_proof_path', 'document_number', 'document_date'];
    
    protected $appends = ['formatted_document_number'];

    public function getFormattedDocumentNumberAttribute(): ?string
    {
        if (!$this->document_number || !$this->document_date) return null;

        $date = \Carbon\Carbon::parse($this->document_date);
        $monthRomani = ['I', 'II', 'III', 'IV', 'V', 'VI', 'VII', 'VIII', 'IX', 'X', 'XI', 'XII'];
        
        $month = str_pad($date->month, 2, '0', STR_PAD_LEFT);
        $romani = $monthRomani[$date->month - 1];
        $year = $date->year;
        
        $paddedNumber = str_pad($this->document_number, 3, '0', STR_PAD_LEFT);

        return "{$month}.{$paddedNumber}/KEPITEKES-BALI/{$romani}/{$year}";
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function studyProgram(): BelongsTo
    {
        return $this->belongsTo(StudyProgram::class);
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

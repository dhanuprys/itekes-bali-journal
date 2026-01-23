<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ResearchSubdetailReviewer extends Model
{
    use HasFactory;

    protected $fillable = ['user_id', 'research_submission_detail_id'];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function detail(): BelongsTo
    {
        return $this->belongsTo(ResearchSubmissionDetail::class, 'research_submission_detail_id');
    }
}

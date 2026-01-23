<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ResearchSubmissionComment extends Model
{
    use HasFactory;

    protected $fillable = ['research_subdetail_id', 'user_id', 'title', 'content'];

    public function detail(): BelongsTo
    {
        return $this->belongsTo(ResearchSubmissionDetail::class, 'research_subdetail_id');
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }
}

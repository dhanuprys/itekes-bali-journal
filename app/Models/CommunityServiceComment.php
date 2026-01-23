<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class CommunityServiceComment extends Model
{
    use HasFactory;

    protected $fillable = ['community_service_subdetail_id', 'title', 'content'];

    public function detail(): BelongsTo
    {
        return $this->belongsTo(CommunityServiceSubmissionDetail::class, 'community_service_subdetail_id');
    }
}

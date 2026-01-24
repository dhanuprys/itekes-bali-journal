<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class CommunityServiceSubmissionComment extends Model
{
    use HasFactory;

    protected $table = 'community_service_comments';

    protected $fillable = ['community_service_subdetail_id', 'user_id', 'content'];

    public function detail(): BelongsTo
    {
        return $this->belongsTo(CommunityServiceSubmissionDetail::class, 'community_service_subdetail_id');
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }
}

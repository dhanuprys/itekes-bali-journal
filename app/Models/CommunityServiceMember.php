<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class CommunityServiceMember extends Model
{
    use HasFactory;

    protected $fillable = ['community_service_subdetail_id', 'name'];

    public function detail(): BelongsTo
    {
        return $this->belongsTo(CommunityServiceSubmissionDetail::class, 'community_service_subdetail_id');
    }
}

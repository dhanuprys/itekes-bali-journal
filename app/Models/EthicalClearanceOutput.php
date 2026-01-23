<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class EthicalClearanceOutput extends Model
{
    use HasFactory;

    protected $fillable = ['user_id', 'ethical_clearance_submission_id'];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function submission(): BelongsTo
    {
        return $this->belongsTo(EthicalClearanceSubmission::class, 'ethical_clearance_submission_id');
    }
}

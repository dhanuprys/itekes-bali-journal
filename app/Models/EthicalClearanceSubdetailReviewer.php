<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class EthicalClearanceSubdetailReviewer extends Model
{
    use HasFactory;

    protected $fillable = ['user_id', 'ethical_clearance_subdetail_id'];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function detail(): BelongsTo
    {
        return $this->belongsTo(EthicalClearanceDetail::class, 'ethical_clearance_subdetail_id');
    }
}

<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class EthicalClearanceComment extends Model
{
    use HasFactory;

    protected $fillable = ['ethical_clearance_subdetail_id', 'title', 'content'];

    public function detail(): BelongsTo
    {
        return $this->belongsTo(EthicalClearanceDetail::class, 'ethical_clearance_subdetail_id');
    }
}

<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class EthicalClearanceMember extends Model
{
    use HasFactory;

    protected $fillable = ['ethical_clearance_subdetail_id', 'name'];

    public function detail(): BelongsTo
    {
        return $this->belongsTo(EthicalClearanceDetail::class, 'ethical_clearance_subdetail_id');
    }
}

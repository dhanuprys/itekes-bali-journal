<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class EthicalClearanceOutputVerification extends Model
{
    use HasFactory;

    protected $fillable = [
        'ethical_clearance_output_id',
        'user_id',
        'status',
        'notes',
    ];

    public function output(): BelongsTo
    {
        return $this->belongsTo(EthicalClearanceOutput::class, 'ethical_clearance_output_id');
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }
}

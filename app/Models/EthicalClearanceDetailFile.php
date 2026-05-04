<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class EthicalClearanceDetailFile extends Model
{
    use HasFactory;

    protected $fillable = [
        'ethical_clearance_detail_id',
        'template_key',
        'file_path',
        'original_name',
    ];

    public function detail(): BelongsTo
    {
        return $this->belongsTo(EthicalClearanceDetail::class, 'ethical_clearance_detail_id');
    }
}

<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class StorageUpload extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'user_id',
        'action',
        'file_path',
        'file_name',
        'file_size',
        'mime_type',
        'disk',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}

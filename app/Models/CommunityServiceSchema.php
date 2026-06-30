<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class CommunityServiceSchema extends Model
{
    use HasFactory, SoftDeletes;

    protected $table = 'community_service_schema';

    protected $fillable = ['title', 'description'];
}

<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class CommunityServiceSchema extends Model
{
    use HasFactory;

    protected $table = 'community_service_schema';
    protected $fillable = ['title', 'description'];
}

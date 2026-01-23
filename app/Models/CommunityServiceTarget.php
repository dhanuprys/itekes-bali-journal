<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class CommunityServiceTarget extends Model
{
    use HasFactory;

    protected $fillable = ['title', 'description'];
}

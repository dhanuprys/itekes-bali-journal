<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ResearchSchema extends Model
{
    use HasFactory;

    protected $table = 'research_schema';
    protected $fillable = ['title', 'description'];
}

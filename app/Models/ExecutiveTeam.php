<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ExecutiveTeam extends Model
{
    use HasFactory;

    protected $table = 'executive_team';

    protected $fillable = [
        'name',
        'position',
        'bio',
        'photo_url',
        'linkedin_url',
    ];
}

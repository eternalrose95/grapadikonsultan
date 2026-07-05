<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Inquiry extends Model
{
    const CREATED_AT = 'submitted_at';
    const UPDATED_AT = null;

    protected $fillable = [
        'full_name',
        'email',
        'phone',
        'subject',
        'message',
    ];
}

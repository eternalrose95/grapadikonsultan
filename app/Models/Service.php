<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Service extends Model
{
    use HasFactory;

    protected $fillable = [
        'service_name',
        'slug',
        'description',
        'icon_url',
        'features',
    ];

    protected $casts = [
        'features' => 'array',
    ];

    public function portfolios()
    {
        return $this->hasMany(Portfolio::class);
    }
}

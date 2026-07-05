<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Portfolio extends Model
{
    use HasFactory;

    protected $fillable = [
        'client_name',
        'location',
        'project_title',
        'project_year',
        'description',
        'image_url',
        'results',
        'service_id',
        'portfolio_category_id',
    ];

    protected $casts = [
        'results' => 'array',
    ];

    public function service()
    {
        return $this->belongsTo(Service::class);
    }

    public function category()
    {
        return $this->belongsTo(PortfolioCategory::class, 'portfolio_category_id');
    }
}

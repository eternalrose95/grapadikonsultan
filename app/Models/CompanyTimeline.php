<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Builder;

class CompanyTimeline extends Model
{
    use HasFactory;

    protected $fillable = [
        'year',
        'title',
        'description',
        'image',
        'is_active',
        'sort_order',
    ];

    protected $casts = [
        'is_active' => 'boolean',
        'sort_order' => 'integer',
    ];

    /**
     * Scope for active timelines
     */
    public function scopeActive(Builder $query): Builder
    {
        return $query->where('is_active', true);
    }

    /**
     * Scope for ordered by year (newest first) or sort_order
     */
    public function scopeOrdered(Builder $query): Builder
    {
        return $query->orderBy('sort_order', 'asc')->orderBy('year', 'asc');
    }

    /**
     * Get the image URL
     */
    public function getImageUrlAttribute(): ?string
    {
        return $this->image ? asset('storage/' . $this->image) : null;
    }
}

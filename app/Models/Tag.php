<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Tag extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'slug',
    ];

    public function articles()
    {
        return $this->belongsToMany(Article::class, 'article_tag');
    }

    /**
     * Get tags with article count, ordered by popularity
     */
    public function scopePopular(Builder $query, int $limit = 10): Builder
    {
        return $query->withCount('articles')
            ->orderBy('articles_count', 'desc')
            ->limit($limit);
    }
}

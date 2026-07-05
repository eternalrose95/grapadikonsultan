<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Article extends Model
{
    use HasFactory;

    protected $fillable = [
        'title',
        'slug',
        'content',
        'excerpt',
        'image_url',
        'author_id',
        'category_id',
        'reading_time',
        'is_featured',
        'is_published',
        'published_at',
        'meta_title',
        'meta_description',
        'views_count',
    ];

    protected $casts = [
        'is_featured' => 'boolean',
        'is_published' => 'boolean',
        'published_at' => 'datetime',
        'views_count' => 'integer',
        'reading_time' => 'integer',
    ];

    public function author()
    {
        return $this->belongsTo(User::class, 'author_id');
    }

    public function category()
    {
        return $this->belongsTo(Category::class);
    }

    public function tags()
    {
        return $this->belongsToMany(Tag::class, 'article_tag')->withTimestamps();
    }

    /**
     * Scope untuk artikel yang sudah dipublish
     */
    public function scopePublished($query)
    {
        return $query->where('is_published', true);
    }

    /**
     * Scope untuk artikel featured
     */
    public function scopeFeatured($query)
    {
        return $query->where('is_featured', true);
    }

    /**
     * Calculate reading time from content (average 200 words/minute)
     */
    public function calculateReadingTime(): int
    {
        $wordCount = str_word_count(strip_tags($this->content));
        return max(1, (int) ceil($wordCount / 200));
    }

    /**
     * Get reading time, auto-calculate if not set
     */
    public function getReadingTimeDisplayAttribute(): int
    {
        return $this->reading_time ?? $this->calculateReadingTime();
    }

    /**
     * Get excerpt, auto-generate from content if not set
     */
    public function getExcerptDisplayAttribute(): string
    {
        if ($this->excerpt) {
            return $this->excerpt;
        }
        return \Illuminate\Support\Str::limit(strip_tags($this->content), 160);
    }

    /**
     * Increment view count
     */
    public function incrementViewCount(): void
    {
        $this->increment('views_count');
    }

    /**
     * Get image URL, with fallback to Unsplash if not set
     * Uses category or title keywords to get relevant image
     */
    public function getImageDisplayAttribute(): string
    {
        // If image_url is set and not empty, use it
        if (!empty($this->image_url)) {
            // Check if it's a storage path or full URL
            if (str_starts_with($this->image_url, 'http')) {
                return $this->image_url;
            }
            return asset('storage/' . $this->image_url);
        }
        
        // Fallback to Unsplash with relevant keywords
        return \App\Helpers\UnsplashHelper::getArticleImage(
            $this->title,
            $this->category?->category_name,
            $this->id,
            800,
            600
        );
    }
}

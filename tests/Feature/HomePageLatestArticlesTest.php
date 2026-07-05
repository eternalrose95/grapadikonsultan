<?php

namespace Tests\Feature;

use App\Models\Article;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use PHPUnit\Framework\Attributes\Test;
use Tests\TestCase;

class HomePageLatestArticlesTest extends TestCase
{
    #[Test]
    public function test_published_articles_are_ordered_by_latest_publication_date_with_created_at_fallback(): void
    {
        Schema::dropIfExists('articles');
        Schema::create('articles', function (Blueprint $table): void {
            $table->id();
            $table->string('title');
            $table->string('slug')->unique();
            $table->longText('content')->nullable();
            $table->text('excerpt')->nullable();
            $table->boolean('is_published')->default(false);
            $table->boolean('is_featured')->default(false);
            $table->timestamp('published_at')->nullable();
            $table->timestamp('created_at')->nullable();
            $table->timestamp('updated_at')->nullable();
            $table->integer('reading_time')->nullable();
            $table->integer('views_count')->default(0);
            $table->string('image_url')->nullable();
            $table->unsignedBigInteger('author_id')->nullable();
            $table->unsignedBigInteger('category_id')->nullable();
        });

        $olderPublishedArticle = Article::create([
            'title' => 'Older published article',
            'slug' => 'older-published-article',
            'content' => 'Older published article content',
            'excerpt' => 'Older published article excerpt',
            'is_published' => true,
            'published_at' => now()->subDays(5),
        ]);
        $olderPublishedArticle->forceFill([
            'created_at' => now()->subDays(5),
            'updated_at' => now()->subDays(5),
        ])->saveQuietly();

        $newerByCreatedArticle = Article::create([
            'title' => 'Newest by created date',
            'slug' => 'newest-by-created-date',
            'content' => 'Newest by created date content',
            'excerpt' => 'Newest by created date excerpt',
            'is_published' => true,
            'published_at' => null,
        ]);
        $newerByCreatedArticle->forceFill([
            'created_at' => now()->subDay(),
            'updated_at' => now()->subDay(),
        ])->saveQuietly();

        $newestPublishedArticle = Article::create([
            'title' => 'Newest published article',
            'slug' => 'newest-published-article',
            'content' => 'Newest published article content',
            'excerpt' => 'Newest published article excerpt',
            'is_published' => true,
            'published_at' => now()->subHours(2),
        ]);
        $newestPublishedArticle->forceFill([
            'created_at' => now()->subDays(10),
            'updated_at' => now()->subDays(10),
        ])->saveQuietly();

        Article::create([
            'title' => 'Unpublished article',
            'slug' => 'unpublished-article',
            'content' => 'Unpublished article content',
            'excerpt' => 'Unpublished article excerpt',
            'is_published' => false,
            'published_at' => now(),
        ]);

        $latestArticles = Article::query()
            ->published()
            ->orderByRaw('CASE WHEN published_at IS NOT NULL THEN 0 ELSE 1 END ASC')
            ->orderByRaw('CASE WHEN published_at IS NOT NULL THEN published_at ELSE created_at END DESC')
            ->orderBy('created_at', 'desc')
            ->take(4)
            ->get();

        $this->assertSame([
            $newestPublishedArticle->id,
            $olderPublishedArticle->id,
            $newerByCreatedArticle->id,
        ], $latestArticles->pluck('id')->all());
    }
}

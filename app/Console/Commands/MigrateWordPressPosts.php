<?php

namespace App\Console\Commands;

use App\Models\Article;
use App\Models\Category;
use App\Models\Tag;
use App\Models\User;
use Corcel\Model\Post;
use Illuminate\Console\Command;
use Illuminate\Support\Str;

class MigrateWordPressPosts extends Command
{
    protected $signature = 'wp:migrate-posts 
                            {--delete : Delete existing articles before importing}
                            {--limit= : Limit the number of posts to import}
                            {--clean : Clean article content during import}';

    protected $description = 'Migrate WordPress posts to Laravel articles';

    private int $imported = 0;
    private int $skipped = 0;
    private int $errors = 0;
    private ?User $defaultAuthor = null;
    private ?Category $defaultCategory = null;

    public function handle(): int
    {
        $this->info('Starting WordPress post migration...');
        $this->newLine();

        // Get default author
        $this->defaultAuthor = User::first();
        if (!$this->defaultAuthor) {
            $this->error('No users found. Please create a user first.');
            return self::FAILURE;
        }

        // Get or create default category
        $this->defaultCategory = Category::firstOrCreate(
            ['slug' => 'uncategorized'],
            ['category_name' => 'Uncategorized', 'description' => 'Uncategorized articles']
        );

        // Delete existing articles if requested
        if ($this->option('delete')) {
            $count = Article::count();
            if ($count > 0) {
                $this->warn("Deleting {$count} existing articles...");
                \DB::statement('SET FOREIGN_KEY_CHECKS=0');
                \DB::table('article_tag')->truncate();
                Article::truncate();
                \DB::statement('SET FOREIGN_KEY_CHECKS=1');
                $this->info('Existing articles deleted.');
            }
        }


        // Get WordPress posts
        $query = Post::on('corcel')
            ->where('post_type', 'post')
            ->where('post_status', 'publish')
            ->orderBy('post_date', 'desc');

        if ($limit = $this->option('limit')) {
            $query->limit((int) $limit);
        }

        $total = $query->count();
        $this->info("Found {$total} published posts to import.");
        $this->newLine();

        $progressBar = $this->output->createProgressBar($total);
        $progressBar->start();

        // Process posts in chunks
        $query->chunk(100, function ($posts) use ($progressBar) {
            foreach ($posts as $post) {
                $this->importPost($post);
                $progressBar->advance();
            }
        });

        $progressBar->finish();
        $this->newLine(2);

        // Summary
        $this->table(
            ['Status', 'Count'],
            [
                ['Imported', $this->imported],
                ['Skipped', $this->skipped],
                ['Errors', $this->errors],
            ]
        );

        $this->info('Migration completed!');

        return self::SUCCESS;
    }

    private function importPost(Post $post): void
    {
        try {
            // Skip if article with same slug exists
            $slug = Str::slug($post->post_name ?: $post->post_title);
            if (empty($slug)) {
                $slug = Str::slug($post->post_title);
            }
            
            // Ensure unique slug
            $originalSlug = $slug;
            $counter = 1;
            while (Article::where('slug', $slug)->exists()) {
                $slug = $originalSlug . '-' . $counter++;
            }

            // Get content
            $content = $post->post_content;
            if ($this->option('clean')) {
                $content = $this->cleanContent($content);
            }

            // Get excerpt
            $excerpt = $post->post_excerpt;
            if (empty($excerpt)) {
                $excerpt = Str::limit(strip_tags($content), 200);
            }

            // Get or create category from WordPress
            $category = $this->getCategory($post);

            // Get featured image
            $imageUrl = $this->getFeaturedImage($post);

            // Create article
            $article = Article::create([
                'title' => $post->post_title,
                'slug' => $slug,
                'content' => $content,
                'excerpt' => $excerpt,
                'image_url' => $imageUrl,
                'author_id' => $this->defaultAuthor->id,
                'category_id' => $category->id,
                'reading_time' => max(1, (int) ceil(str_word_count(strip_tags($content)) / 200)),
                'is_featured' => false,
                'is_published' => true,
                'published_at' => $post->post_date,
                'meta_title' => Str::limit($post->post_title, 60),
                'meta_description' => Str::limit(strip_tags($excerpt), 160),
                'views_count' => 0,
            ]);

            // Sync tags
            $this->syncTags($article, $post);

            $this->imported++;
        } catch (\Exception $e) {
            $this->errors++;
            $this->newLine();
            $this->error("Error importing post '{$post->post_title}': " . $e->getMessage());
        }
    }

    private function getCategory(Post $post): Category
    {
        // Try to get WordPress category
        try {
            $wpCategories = $post->taxonomies()
                ->where('taxonomy', 'category')
                ->get();

            if ($wpCategories->isNotEmpty()) {
                $wpCategory = $wpCategories->first();
                $categoryName = $wpCategory->term->name ?? 'Uncategorized';
                $categorySlug = Str::slug($wpCategory->term->slug ?? $categoryName);

                return Category::firstOrCreate(
                    ['slug' => $categorySlug],
                    ['category_name' => $categoryName, 'description' => '']
                );
            }
        } catch (\Exception $e) {
            // Ignore taxonomy errors
        }

        return $this->defaultCategory;
    }

    private function syncTags(Article $article, Post $post): void
    {
        try {
            $wpTags = $post->taxonomies()
                ->where('taxonomy', 'post_tag')
                ->get();

            $tagIds = [];
            foreach ($wpTags as $wpTag) {
                $tagName = $wpTag->term->name ?? null;
                if ($tagName) {
                    $tag = Tag::firstOrCreate(
                        ['slug' => Str::slug($tagName)],
                        ['tag_name' => $tagName]
                    );
                    $tagIds[] = $tag->id;
                }
            }

            if (!empty($tagIds)) {
                $article->tags()->sync($tagIds);
            }
        } catch (\Exception $e) {
            // Ignore tag sync errors
        }
    }

    private function getFeaturedImage(Post $post): ?string
    {
        try {
            $thumbnail = $post->thumbnail;
            if ($thumbnail) {
                return $thumbnail->guid ?? null;
            }
        } catch (\Exception $e) {
            // Ignore thumbnail errors
        }

        return null;
    }

    private function cleanContent(string $content): string
    {
        // Remove WordPress Gutenberg block comments
        $content = preg_replace('/<!--\s*\/?wp:[^>]*-->/s', '', $content);
        
        // Remove empty paragraphs
        $content = preg_replace('/<p>\s*<\/p>/i', '', $content);
        
        // Remove WordPress block classes
        $content = preg_replace('/class="wp-block-[^"]*"/i', '', $content);
        
        // Clean up multiple newlines
        $content = preg_replace('/\n{3,}/', "\n\n", $content);
        
        // Trim whitespace
        $content = trim($content);
        
        return $content;
    }
}

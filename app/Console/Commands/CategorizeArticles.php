<?php

namespace App\Console\Commands;

use App\Models\Article;
use App\Models\Category;
use App\Models\Tag;
use Illuminate\Console\Command;
use Illuminate\Support\Str;

class CategorizeArticles extends Command
{
    protected $signature = 'articles:categorize 
                            {--reset : Reset all categories to Uncategorized first}
                            {--tags : Also generate tags from content}';

    protected $description = 'Auto-categorize articles based on title and content keywords';

    // Kategori dan kata kunci terkait
    private array $categoryKeywords = [
        'Studi Kelayakan' => [
            'studi kelayakan', 'feasibility study', 'kelayakan usaha', 'kelayakan bisnis',
            'analisis kelayakan', 'fs ', 'aspek kelayakan', 'evaluasi proyek'
        ],
        'Bisnis Plan' => [
            'bisnis plan', 'business plan', 'rencana bisnis', 'proposal bisnis',
            'proposal usaha', 'perencanaan bisnis', 'rencana usaha'
        ],
        'Riset Pasar' => [
            'riset pasar', 'market research', 'survei pasar', 'analisis pasar',
            'penelitian pasar', 'studi pasar', 'market survey', 'consumer research'
        ],
        'Konsultasi Bisnis' => [
            'konsultan', 'konsultasi', 'advisory', 'jasa konsultan',
            'consulting', 'business consultant', 'konsultan bisnis'
        ],
        'Marketing' => [
            'marketing', 'pemasaran', 'digital marketing', 'strategi pemasaran',
            'promosi', 'branding', 'advertising', 'iklan', 'sosial media'
        ],
        'Analisis Keuangan' => [
            'keuangan', 'finansial', 'financial', 'analisis keuangan', 'laporan keuangan',
            'cash flow', 'roi', 'npv', 'irr', 'payback period', 'proyeksi keuangan',
            'aspek finansial', 'aspek keuangan', 'modal', 'investasi'
        ],
        'Properti' => [
            'properti', 'property', 'real estate', 'rumah', 'apartemen', 'hotel',
            'perumahan', 'gedung', 'bangunan', 'tanah', 'residensial', 'komersial'
        ],
        'Strategi Bisnis' => [
            'strategi bisnis', 'business strategy', 'swot', 'manajemen strategis',
            'analisis kompetitor', 'competitive advantage', 'strategi perusahaan'
        ],
    ];

    // Tag keywords untuk ekstraksi
    private array $tagKeywords = [
        'Studi Kelayakan', 'Bisnis Plan', 'Feasibility Study', 'Riset Pasar',
        'Analisis Keuangan', 'ROI', 'NPV', 'IRR', 'SWOT', 'Marketing',
        'Digital Marketing', 'Property', 'Hotel', 'Perumahan', 'Apartemen',
        'Retail', 'Restoran', 'Cafe', 'F&B', 'Manufaktur', 'Industri',
        'Pertanian', 'Perikanan', 'Pariwisata', 'Kesehatan', 'Pendidikan',
        'Teknologi', 'E-commerce', 'Startup', 'UMKM', 'Franchise',
        'Investasi', 'Modal Usaha', 'Kredit', 'Bank', 'Leasing',
    ];

    private int $categorized = 0;
    private int $tagged = 0;
    private array $categoryCache = [];

    public function handle(): int
    {
        $this->info('Starting article categorization...');
        $this->newLine();

        // Load categories
        $this->loadCategories();

        // Reset if requested
        if ($this->option('reset')) {
            $uncategorized = Category::where('slug', 'uncategorized')->first();
            if ($uncategorized) {
                Article::query()->update(['category_id' => $uncategorized->id]);
                $this->warn('All articles reset to Uncategorized.');
            }
        }

        $articles = Article::all();
        $progressBar = $this->output->createProgressBar($articles->count());
        $progressBar->start();

        foreach ($articles as $article) {
            $this->categorizeArticle($article);
            
            if ($this->option('tags')) {
                $this->tagArticle($article);
            }
            
            $progressBar->advance();
        }

        $progressBar->finish();
        $this->newLine(2);

        // Summary
        $this->table(
            ['Action', 'Count'],
            [
                ['Categorized', $this->categorized],
                ['Tagged', $this->tagged],
            ]
        );

        // Show category distribution
        $this->newLine();
        $this->info('Category Distribution:');
        $categories = Category::withCount('articles')
            ->orderByDesc('articles_count')
            ->get();
        
        $rows = [];
        foreach ($categories as $cat) {
            $rows[] = [$cat->category_name, $cat->articles_count];
        }
        $this->table(['Category', 'Articles'], $rows);

        return self::SUCCESS;
    }

    private function loadCategories(): void
    {
        foreach (array_keys($this->categoryKeywords) as $categoryName) {
            $slug = Str::slug($categoryName);
            $category = Category::firstOrCreate(
                ['slug' => $slug],
                ['category_name' => $categoryName, 'description' => '']
            );
            $this->categoryCache[$categoryName] = $category;
        }
    }

    private function categorizeArticle(Article $article): void
    {
        $text = strtolower($article->title . ' ' . strip_tags($article->content));
        
        $bestMatch = null;
        $highestScore = 0;

        foreach ($this->categoryKeywords as $categoryName => $keywords) {
            $score = 0;
            foreach ($keywords as $keyword) {
                $count = substr_count($text, strtolower($keyword));
                if ($count > 0) {
                    // Weight by keyword length (longer = more specific = higher score)
                    $score += $count * strlen($keyword);
                }
            }
            
            if ($score > $highestScore) {
                $highestScore = $score;
                $bestMatch = $categoryName;
            }
        }

        if ($bestMatch && isset($this->categoryCache[$bestMatch])) {
            $article->category_id = $this->categoryCache[$bestMatch]->id;
            $article->save();
            $this->categorized++;
        }
    }

    private function tagArticle(Article $article): void
    {
        $text = strtolower($article->title . ' ' . strip_tags($article->content));
        $tagIds = [];

        foreach ($this->tagKeywords as $tagName) {
            if (str_contains($text, strtolower($tagName))) {
                $tag = Tag::firstOrCreate(
                    ['slug' => Str::slug($tagName)],
                    ['name' => $tagName]
                );
                $tagIds[] = $tag->id;
            }
        }

        if (!empty($tagIds)) {
            // Limit to 5 most relevant tags
            $tagIds = array_slice($tagIds, 0, 5);
            $article->tags()->syncWithoutDetaching($tagIds);
            $this->tagged++;
        }
    }
}

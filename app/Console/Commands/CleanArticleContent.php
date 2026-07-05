<?php

namespace App\Console\Commands;

use App\Models\Article;
use Illuminate\Console\Command;

class CleanArticleContent extends Command
{
    protected $signature = 'articles:clean 
                            {--dry-run : Preview changes without saving}';

    protected $description = 'Clean imported article content from WordPress artifacts';

    public function handle()
    {
        $dryRun = $this->option('dry-run');

        if ($dryRun) {
            $this->warn('🔍 DRY RUN MODE - No changes will be saved');
            $this->newLine();
        }

        $articles = Article::all();
        $this->info("📝 Processing {$articles->count()} articles...");
        $this->newLine();

        $bar = $this->output->createProgressBar($articles->count());
        $bar->start();

        $cleaned = 0;

        foreach ($articles as $article) {
            $originalContent = $article->content;
            $cleanedContent = $this->cleanContent($originalContent);

            if ($originalContent !== $cleanedContent) {
                if (!$dryRun) {
                    $article->content = $cleanedContent;
                    $article->save();
                }
                $cleaned++;
            }

            $bar->advance();
        }

        $bar->finish();
        $this->newLine(2);

        $this->info("✅ Cleaned {$cleaned} articles");

        if ($dryRun) {
            $this->warn('   Run without --dry-run to apply changes.');
        }

        return Command::SUCCESS;
    }

    protected function cleanContent(string $content): string
    {
        // Remove WordPress Gutenberg block comments
        $content = preg_replace('/<!--\s*\/?wp:[^>]*-->/s', '', $content);
        
        // Remove literal 'rn', 'rnrn', 'nnnn' (escaped newlines/CRLF)
        $content = preg_replace('/(rn){2,}/', "\n\n", $content);
        $content = preg_replace('/(rn)+/', "\n", $content);
        $content = preg_replace('/n{4,}/', "\n\n", $content);
        
        // Remove literal 'nn' patterns between tags
        $content = preg_replace('/(?<=>)nn(?=<)/', "\n", $content);
        
        // Remove single 'n' or 'rn' at start of list items
        $content = preg_replace('/<ul>\s*(?:rn|n)\s*<li>/', "<ul>\n<li>", $content);
        $content = preg_replace('/<\/li>\s*(?:rn|n)\s*<\/ul>/', "</li>\n</ul>", $content);
        
        // Remove standalone 'n' or 'rn' between tags  
        $content = preg_replace('/(?<=>)\s*(?:rn|n)\s*(?=<)/', "\n", $content);
        
        // Remove WordPress block classes
        $content = preg_replace('/\s*class="wp-block-[^"]*"/', '', $content);
        
        // Remove empty paragraphs
        $content = preg_replace('/<p>\s*<\/p>/', '', $content);
        
        // Clean up excessive whitespace/newlines
        $content = preg_replace('/\n{3,}/', "\n\n", $content);
        
        // Remove leading 'n' or 'rn' followed by whitespace at start of content
        $content = preg_replace('/^(?:rn|n)+\s+/', '', $content);
        
        // Remove trailing 'n' or 'rn' at end of content
        $content = preg_replace('/\s*(?:rn|n)\s*$/', '', $content);
        
        // Remove any remaining 'rn' that appears as a standalone word (careful not to delete inside words like 'modern')
        // Only remove 'rn' if it stays on its own line or surrounded by spaces
        $content = preg_replace('/\s+rn\s+/', ' ', $content);

        // Remove leading/trailing whitespace
        $content = trim($content);

        return $content;
    }
}

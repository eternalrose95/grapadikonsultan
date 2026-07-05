<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Spatie\Sitemap\SitemapGenerator;

class GenerateSitemap extends Command
{
    protected $signature = 'sitemap:generate';

    protected $description = 'Generate the sitemap XML files for the website';

    public function handle(): int
    {
        $this->info('Generating sitemap...');

        $baseUrl = rtrim(config('app.url', 'http://localhost'), '/');

        SitemapGenerator::create($baseUrl)
            ->writeToFile(public_path('sitemap.xml'));

        $this->info('Sitemap generated at ' . public_path('sitemap.xml'));

        return self::SUCCESS;
    }
}

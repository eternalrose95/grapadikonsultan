<?php

namespace App\Console\Commands;

use App\Models\Article;
use App\Models\Service;
use App\Support\SitemapXmlBuilder;
use Illuminate\Console\Command;
// use Illuminate\Support\Facades\URL;

class GenerateSitemap extends Command
{
    protected $signature = 'sitemap:generate';

    protected $description = 'Generate the sitemap XML files for the website';

    public function handle(): int
    {
        $this->info('Generating sitemap...');

        $baseUrl = rtrim(config('app.url', 'http://localhost'), '/');

        $pages = [
            ['loc' => $baseUrl . '/', 'lastmod' => now()->toDateString(), 'changefreq' => 'daily', 'priority' => '1.0'],
            ['loc' => $baseUrl . '/about', 'lastmod' => now()->toDateString(), 'changefreq' => 'monthly', 'priority' => '0.8'],
            ['loc' => $baseUrl . '/services', 'lastmod' => now()->toDateString(), 'changefreq' => 'weekly', 'priority' => '0.9'],
            ['loc' => $baseUrl . '/portfolio', 'lastmod' => now()->toDateString(), 'changefreq' => 'weekly', 'priority' => '0.8'],
            ['loc' => $baseUrl . '/blog', 'lastmod' => now()->toDateString(), 'changefreq' => 'daily', 'priority' => '0.9'],
            ['loc' => $baseUrl . '/timeline', 'lastmod' => now()->toDateString(), 'changefreq' => 'monthly', 'priority' => '0.6'],
            ['loc' => $baseUrl . '/contact', 'lastmod' => now()->toDateString(), 'changefreq' => 'monthly', 'priority' => '0.7'],
        ];

        $blogUrls = Article::published()
            ->orderBy('updated_at', 'desc')
            ->get()
            ->map(function ($article) use ($baseUrl) {
                return [
                    'loc' => $baseUrl . '/blog/' . $article->slug,
                    'lastmod' => $article->updated_at?->toDateString() ?? now()->toDateString(),
                    'changefreq' => 'weekly',
                    'priority' => $article->is_featured ? '0.9' : '0.7',
                ];
            })
            ->toArray();

        $serviceUrls = Service::query()->get()->map(function ($service) use ($baseUrl) {
            return [
                'loc' => $baseUrl . '/services/' . $service->slug,
                'lastmod' => $service->updated_at?->toDateString() ?? now()->toDateString(),
                'changefreq' => 'monthly',
                'priority' => '0.8',
            ];
        })->toArray();

        file_put_contents(public_path('sitemap.xml'), SitemapXmlBuilder::buildUrlSet($pages));
        file_put_contents(public_path('sitemap-pages.xml'), SitemapXmlBuilder::buildUrlSet($pages));
        file_put_contents(public_path('sitemap-blog.xml'), SitemapXmlBuilder::buildUrlSet($blogUrls));
        file_put_contents(public_path('sitemap-services.xml'), SitemapXmlBuilder::buildUrlSet($serviceUrls));

        $indexXml = SitemapXmlBuilder::buildIndex([
            ['loc' => $baseUrl . '/sitemap-pages.xml', 'lastmod' => now()->toDateString()],
            ['loc' => $baseUrl . '/sitemap-blog.xml', 'lastmod' => now()->toDateString()],
            ['loc' => $baseUrl . '/sitemap-services.xml', 'lastmod' => now()->toDateString()],
        ]);
        file_put_contents(public_path('sitemap-index.xml'), $indexXml);

        $this->info('Sitemap generated at ' . public_path('sitemap.xml'));

        return self::SUCCESS;
    }
}

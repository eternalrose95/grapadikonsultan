<?php

namespace Tests\Unit;

use App\Support\SitemapXmlBuilder;
use PHPUnit\Framework\TestCase;

class SitemapXmlBuilderTest extends TestCase
{
    public function test_it_builds_sitemap_index_xml(): void
    {
        $xml = SitemapXmlBuilder::buildIndex([
            ['loc' => 'https://example.com/sitemap-pages.xml', 'lastmod' => '2026-07-05'],
            ['loc' => 'https://example.com/sitemap-blog.xml', 'lastmod' => '2026-07-05'],
        ]);

        $this->assertStringContainsString('<sitemapindex', $xml);
        $this->assertStringContainsString('<loc>https://example.com/sitemap-pages.xml</loc>', $xml);
        $this->assertStringContainsString('<loc>https://example.com/sitemap-blog.xml</loc>', $xml);
    }

    public function test_it_builds_url_set_xml(): void
    {
        $xml = SitemapXmlBuilder::buildUrlSet([
            [
                'loc' => 'https://example.com/',
                'lastmod' => '2026-07-05',
                'changefreq' => 'daily',
                'priority' => '1.0',
            ],
        ]);

        $this->assertStringContainsString('<urlset', $xml);
        $this->assertStringContainsString('<loc>https://example.com/</loc>', $xml);
        $this->assertStringContainsString('<changefreq>daily</changefreq>', $xml);
    }
}

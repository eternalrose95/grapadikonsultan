<?php

namespace App\Http\Controllers;

use App\Models\Article;
use App\Models\Service;
use Illuminate\Support\Facades\URL;
use Spatie\Sitemap\Tags\Url as SitemapUrl;
use Spatie\Sitemap\Sitemap;
use Spatie\Sitemap\SitemapIndex;

class SitemapController extends Controller
{
    /**
     * Generate main sitemap index
     */
    public function index()
    {
        $sitemapIndex = SitemapIndex::create()
            ->add(URL::to('/sitemap-pages.xml'))
            ->add(URL::to('/sitemap-blog.xml'))
            ->add(URL::to('/sitemap-services.xml'));

        return response($sitemapIndex->render(), 200)
            ->header('Content-Type', 'application/xml');
    }

    /**
     * Generate sitemap for static pages
     */
    public function pages()
    {
        $sitemap = Sitemap::create()
            ->add(SitemapUrl::create(URL::to('/'))
                ->setLastModificationDate(now())
                ->setChangeFrequency(SitemapUrl::CHANGE_FREQUENCY_DAILY)
                ->setPriority(1.0))
            ->add(SitemapUrl::create(URL::to('/about'))
                ->setLastModificationDate(now())
                ->setChangeFrequency(SitemapUrl::CHANGE_FREQUENCY_MONTHLY)
                ->setPriority(0.8))
            ->add(SitemapUrl::create(URL::to('/services'))
                ->setLastModificationDate(now())
                ->setChangeFrequency(SitemapUrl::CHANGE_FREQUENCY_WEEKLY)
                ->setPriority(0.9))
            ->add(SitemapUrl::create(URL::to('/portfolio'))
                ->setLastModificationDate(now())
                ->setChangeFrequency(SitemapUrl::CHANGE_FREQUENCY_WEEKLY)
                ->setPriority(0.8))
            ->add(SitemapUrl::create(URL::to('/blog'))
                ->setLastModificationDate(now())
                ->setChangeFrequency(SitemapUrl::CHANGE_FREQUENCY_DAILY)
                ->setPriority(0.9))
            ->add(SitemapUrl::create(URL::to('/timeline'))
                ->setLastModificationDate(now())
                ->setChangeFrequency(SitemapUrl::CHANGE_FREQUENCY_MONTHLY)
                ->setPriority(0.6))
            ->add(SitemapUrl::create(URL::to('/contact'))
                ->setLastModificationDate(now())
                ->setChangeFrequency(SitemapUrl::CHANGE_FREQUENCY_MONTHLY)
                ->setPriority(0.7));

        return response($sitemap->render(), 200)
            ->header('Content-Type', 'application/xml');
    }

    /**
     * Generate sitemap for blog articles
     */
    public function blog()
    {
        $sitemap = Sitemap::create();

        Article::published()
            ->orderBy('updated_at', 'desc')
            ->get()
            ->each(function ($article) use ($sitemap) {
                $sitemap->add(
                    SitemapUrl::create(URL::to('/blog/' . $article->slug))
                        ->setLastModificationDate($article->updated_at)
                        ->setChangeFrequency(SitemapUrl::CHANGE_FREQUENCY_WEEKLY)
                        ->setPriority($article->is_featured ? 0.9 : 0.7)
                );
            });

        return response($sitemap->render(), 200)
            ->header('Content-Type', 'application/xml');
    }

    /**
     * Generate sitemap for services
     */
    public function services()
    {
        $sitemap = Sitemap::create();

        Service::query()->get()->each(function ($service) use ($sitemap) {
            $sitemap->add(
                SitemapUrl::create(URL::to('/services/' . $service->slug))
                    ->setLastModificationDate($service->updated_at)
                    ->setChangeFrequency(SitemapUrl::CHANGE_FREQUENCY_MONTHLY)
                    ->setPriority(0.8)
            );
        });

        return response($sitemap->render(), 200)
            ->header('Content-Type', 'application/xml');
    }
}

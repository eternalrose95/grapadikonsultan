<?php

namespace App\Providers;

use App\Models\SiteSetting;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        // Batch-load all site settings in a single query
        // This prevents N+1 queries from site_setting() calls in views
        try {
            SiteSetting::loadAll();
        } catch (\Exception $e) {
            // Silently fail if DB is not available (e.g. during migrations)
        }
    }
}

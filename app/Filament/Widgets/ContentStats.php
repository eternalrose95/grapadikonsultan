<?php

namespace App\Filament\Widgets;

use App\Models\Article;
use App\Models\Portfolio;
use App\Models\Service;
use App\Models\Category;
use Filament\Widgets\StatsOverviewWidget as BaseWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;
use Carbon\Carbon;

class ContentStats extends BaseWidget
{
    protected static ?int $sort = 1;

    protected function getStats(): array
    {
        $articlesThisMonth = Article::whereMonth('created_at', Carbon::now()->month)
            ->whereYear('created_at', Carbon::now()->year)
            ->count();

        return [
            Stat::make('Total Artikel', Article::count())
                ->description("{$articlesThisMonth} bulan ini")
                ->descriptionIcon('heroicon-m-document-text')
                ->color('info'),
            Stat::make('Total Portfolio', Portfolio::count())
                ->description('Proyek selesai')
                ->descriptionIcon('heroicon-m-folder')
                ->color('success'),
            Stat::make('Total Layanan', Service::count())
                ->description('Layanan aktif')
                ->descriptionIcon('heroicon-m-briefcase')
                ->color('warning'),
            Stat::make('Total Kategori', Category::count())
                ->description('Kategori artikel')
                ->descriptionIcon('heroicon-m-tag')
                ->color('primary'),
        ];
    }
}

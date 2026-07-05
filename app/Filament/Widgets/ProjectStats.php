<?php

namespace App\Filament\Widgets;

use App\Models\Project;
use Filament\Widgets\StatsOverviewWidget as BaseWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;
use Carbon\Carbon;

class ProjectStats extends BaseWidget
{
    protected static ?int $sort = 4;

    public static function canView(): bool
    {
        return request()->routeIs('filament.admin.pages.crm-dashboard');
    }

    protected function getStats(): array
    {
        $activeProjects = Project::where('status', 'active')->count();
        
        $deadlineThisWeek = Project::where('status', 'active')
            ->whereBetween('deadline', [Carbon::now(), Carbon::now()->endOfWeek()])
            ->count();
        
        $completedThisMonth = Project::where('status', 'completed')
            ->whereMonth('updated_at', Carbon::now()->month)
            ->whereYear('updated_at', Carbon::now()->year)
            ->count();

        return [
            Stat::make('Proyek Berjalan Ini', $activeProjects)
                ->icon('heroicon-o-briefcase')
                ->color('warning'),
            Stat::make('Deadline Minggu Ini', $deadlineThisWeek)
                ->icon('heroicon-o-clock')
                ->color('danger'),
            Stat::make('Selesai Bulan Ini', $completedThisMonth)
                ->icon('heroicon-o-check-circle')
                ->color('success'),
        ];
    }
}

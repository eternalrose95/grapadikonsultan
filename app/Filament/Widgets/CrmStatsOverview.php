<?php

namespace App\Filament\Widgets;

use App\Models\Lead;
use App\Models\Client;
use App\Models\Interaction;
use Filament\Widgets\StatsOverviewWidget as BaseWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;
use Carbon\Carbon;

class CrmStatsOverview extends BaseWidget
{
    protected static ?int $sort = 1;

    protected function getStats(): array
    {
        // Leads stats
        $newLeadsThisMonth = Lead::whereMonth('created_at', Carbon::now()->month)
            ->whereYear('created_at', Carbon::now()->year)
            ->count();
        $newLeadsLastMonth = Lead::whereMonth('created_at', Carbon::now()->subMonth()->month)
            ->whereYear('created_at', Carbon::now()->subMonth()->year)
            ->count();
        $leadsTrend = $newLeadsLastMonth > 0 
            ? round((($newLeadsThisMonth - $newLeadsLastMonth) / $newLeadsLastMonth) * 100, 0)
            : ($newLeadsThisMonth > 0 ? 100 : 0);

        // Deals stats
        $dealsThisMonth = Lead::where('status', 'deal')
            ->whereMonth('updated_at', Carbon::now()->month)
            ->whereYear('updated_at', Carbon::now()->year)
            ->count();

        // Active clients
        $activeClients = Client::where('status', 'active')->count();

        // Pending follow-ups
        $pendingFollowUps = Interaction::whereNotNull('follow_up_at')
            ->where('follow_up_completed', false)
            ->where('follow_up_at', '<=', now()->endOfDay())
            ->count();

        // Revenue from deals this month
        $revenueThisMonth = Lead::where('status', 'deal')
            ->whereMonth('updated_at', Carbon::now()->month)
            ->whereYear('updated_at', Carbon::now()->year)
            ->sum('value');
        $revenueLastMonth = Lead::where('status', 'deal')
            ->whereMonth('updated_at', Carbon::now()->subMonth()->month)
            ->whereYear('updated_at', Carbon::now()->subMonth()->year)
            ->sum('value');
        $revenueTrend = $revenueLastMonth > 0 
            ? round((($revenueThisMonth - $revenueLastMonth) / $revenueLastMonth) * 100, 0)
            : ($revenueThisMonth > 0 ? 100 : 0);

        return [
            Stat::make('Leads Baru Bulan Ini', $newLeadsThisMonth)
                ->description($leadsTrend >= 0 ? "+{$leadsTrend}% dari bulan lalu" : "{$leadsTrend}% dari bulan lalu")
                ->descriptionIcon($leadsTrend >= 0 ? 'heroicon-m-arrow-trending-up' : 'heroicon-m-arrow-trending-down')
                ->color('info')
                ->chart([7, 3, 4, 5, 6, 3, 5, $newLeadsThisMonth]),
            Stat::make('Deals Bulan Ini', $dealsThisMonth)
                ->description('Lead → Client')
                ->descriptionIcon('heroicon-m-check-circle')
                ->color('success')
                ->chart([2, 4, 3, 5, 4, 6, 5, $dealsThisMonth]),
            Stat::make('Active Clients', $activeClients)
                ->description('Total client aktif')
                ->descriptionIcon('heroicon-m-building-office-2')
                ->color('primary'),
            Stat::make('Follow-up Hari Ini', $pendingFollowUps)
                ->description($pendingFollowUps > 0 ? 'Perlu ditindaklanjuti' : 'Semua sudah selesai')
                ->descriptionIcon($pendingFollowUps > 0 ? 'heroicon-m-exclamation-circle' : 'heroicon-m-check-circle')
                ->color($pendingFollowUps > 0 ? 'warning' : 'success'),
        ];
    }
}

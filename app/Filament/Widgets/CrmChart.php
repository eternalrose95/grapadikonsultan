<?php

namespace App\Filament\Widgets;

use App\Models\Lead;
use Filament\Widgets\ChartWidget;
use Carbon\Carbon;

class CrmChart extends ChartWidget
{
    protected static ?string $heading = 'Statistik CRM';

    protected static ?int $sort = 2;

    protected int | string | array $columnSpan = 'full';

    protected static ?string $maxHeight = '300px';

    public static function canView(): bool
    {
        return request()->routeIs('filament.admin.pages.crm-dashboard');
    }

    protected function getData(): array
    {
        $leadsData = [];
        $dealsData = [];
        $labels = [];

        // Get last 6 months of data
        for ($i = 5; $i >= 0; $i--) {
            $date = Carbon::now()->subMonths($i);
            
            $leadsCount = Lead::whereMonth('created_at', $date->month)
                ->whereYear('created_at', $date->year)
                ->count();
            
            $dealsCount = Lead::where('status', 'deal')
                ->whereMonth('updated_at', $date->month)
                ->whereYear('updated_at', $date->year)
                ->count();
            
            $leadsData[] = $leadsCount;
            $dealsData[] = $dealsCount;
            $labels[] = $date->format('M');
        }

        return [
            'datasets' => [
                [
                    'label' => 'Leads',
                    'data' => $leadsData,
                    'borderColor' => 'rgb(34, 197, 94)',
                    'backgroundColor' => 'rgba(34, 197, 94, 0.1)',
                    'fill' => true,
                    'tension' => 0.4,
                ],
                [
                    'label' => 'Deals',
                    'data' => $dealsData,
                    'borderColor' => 'rgb(251, 146, 60)',
                    'backgroundColor' => 'rgba(251, 146, 60, 0.1)',
                    'fill' => true,
                    'tension' => 0.4,
                ],
            ],
            'labels' => $labels,
        ];
    }

    protected function getType(): string
    {
        return 'line';
    }
}

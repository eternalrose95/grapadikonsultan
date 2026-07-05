<?php

namespace App\Filament\Widgets;

use App\Models\Project;
use Filament\Widgets\ChartWidget;

class ProjectDistributionChart extends ChartWidget
{
    protected static ?string $heading = 'Daftar Proyek';

    protected static ?int $sort = 6;

    protected int | string | array $columnSpan = 1;

    protected static ?string $maxHeight = '250px';

    public static function canView(): bool
    {
        return request()->routeIs('filament.admin.pages.crm-dashboard');
    }

    protected function getData(): array
    {
        $types = ['development', 'marketing', 'execution', 'design', 'consulting'];
        $data = [];
        $labels = [];
        $colors = [];

        $colorMap = [
            'development' => 'rgb(59, 130, 246)',
            'marketing' => 'rgb(34, 197, 94)',
            'execution' => 'rgb(251, 146, 60)',
            'design' => 'rgb(168, 85, 247)',
            'consulting' => 'rgb(107, 114, 128)',
        ];

        $labelMap = [
            'development' => 'Development',
            'marketing' => 'Marketing',
            'execution' => 'Execution',
            'design' => 'Design',
            'consulting' => 'Consulting',
        ];

        foreach ($types as $type) {
            $count = Project::where('type', $type)->where('status', 'active')->count();
            if ($count > 0) {
                $data[] = $count;
                $labels[] = $labelMap[$type];
                $colors[] = $colorMap[$type];
            }
        }

        return [
            'datasets' => [
                [
                    'data' => $data,
                    'backgroundColor' => $colors,
                ],
            ],
            'labels' => $labels,
        ];
    }

    protected function getType(): string
    {
        return 'bar';
    }

    protected function getOptions(): array
    {
        return [
            'indexAxis' => 'y',
            'scales' => [
                'x' => [
                    'display' => true,
                    'ticks' => [
                        'stepSize' => 1,
                    ],
                ],
            ],
            'plugins' => [
                'legend' => [
                    'display' => false,
                ],
            ],
        ];
    }
}

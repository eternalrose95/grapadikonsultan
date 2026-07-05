<?php

namespace App\Filament\Widgets;

use App\Models\Lead;
use Filament\Widgets\ChartWidget;

class LeadsBySourceChart extends ChartWidget
{
    protected static ?string $heading = 'Leads by Source';

    protected static ?int $sort = 3;

    protected int | string | array $columnSpan = 1;

    protected function getType(): string
    {
        return 'doughnut';
    }

    protected function getData(): array
    {
        $sources = Lead::SOURCE_OPTIONS;
        $data = [];
        $colors = [];

        foreach ($sources as $key => $label) {
            $count = Lead::where('source', $key)->count();
            if ($count > 0) {
                $data[$key] = $count;
                $colors[$key] = match ($key) {
                    'web_wa' => 'rgb(34, 197, 94)',     // green
                    'manual' => 'rgb(107, 114, 128)',   // gray
                    'referral' => 'rgb(59, 130, 246)', // blue
                    'social' => 'rgb(168, 85, 247)',   // purple
                    'event' => 'rgb(245, 158, 11)',    // amber
                    default => 'rgb(107, 114, 128)',
                };
            }
        }

        return [
            'datasets' => [
                [
                    'data' => array_values($data),
                    'backgroundColor' => array_values($colors),
                ],
            ],
            'labels' => array_map(fn ($key) => $sources[$key], array_keys($data)),
        ];
    }

    protected function getOptions(): array
    {
        return [
            'plugins' => [
                'legend' => [
                    'position' => 'bottom',
                ],
            ],
        ];
    }
}

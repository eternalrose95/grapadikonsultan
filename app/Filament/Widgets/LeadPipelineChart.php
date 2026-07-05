<?php

namespace App\Filament\Widgets;

use App\Models\Lead;
use Filament\Widgets\ChartWidget;

class LeadPipelineChart extends ChartWidget
{
    protected static ?string $heading = 'Lead Pipeline';

    protected static ?int $sort = 2;

    protected int | string | array $columnSpan = 1;

    protected function getType(): string
    {
        return 'bar';
    }

    protected function getData(): array
    {
        $statuses = Lead::STATUS_OPTIONS;
        $data = [];
        $colors = [];

        foreach ($statuses as $key => $label) {
            $data[$key] = Lead::where('status', $key)->count();
            $colors[$key] = match ($key) {
                'new' => 'rgb(59, 130, 246)',       // blue
                'contacted' => 'rgb(245, 158, 11)', // amber
                'meeting' => 'rgb(99, 102, 241)',   // indigo
                'proposal' => 'rgb(168, 85, 247)',  // purple
                'negotiation' => 'rgb(236, 72, 153)', // pink
                'deal' => 'rgb(34, 197, 94)',       // green
                'rejected' => 'rgb(239, 68, 68)',   // red
                default => 'rgb(107, 114, 128)',    // gray
            };
        }

        return [
            'datasets' => [
                [
                    'label' => 'Leads',
                    'data' => array_values($data),
                    'backgroundColor' => array_values($colors),
                ],
            ],
            'labels' => array_values($statuses),
        ];
    }

    protected function getOptions(): array
    {
        return [
            'plugins' => [
                'legend' => [
                    'display' => false,
                ],
            ],
            'scales' => [
                'y' => [
                    'beginAtZero' => true,
                    'ticks' => [
                        'stepSize' => 1,
                    ],
                ],
            ],
        ];
    }
}

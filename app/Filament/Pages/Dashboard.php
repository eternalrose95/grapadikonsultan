<?php

namespace App\Filament\Pages;

use Filament\Pages\Page;
use Filament\Actions\Action;
use Livewire\Attributes\Url;
use App\Filament\Widgets\ContentStats;
use App\Filament\Widgets\CrmStatsOverview;
use App\Filament\Widgets\CrmChart;
use App\Filament\Widgets\LatestLeads;
use App\Filament\Widgets\ProjectStats;
use App\Filament\Widgets\ProjectList;
use App\Filament\Widgets\ProjectDistributionChart;

class Dashboard extends Page
{
    protected static ?string $navigationIcon = 'heroicon-o-home';

    protected static string $view = 'filament.pages.dashboard';

    protected static ?string $title = 'Dashboard';

    protected static ?string $navigationLabel = 'Dashboard';

    protected static ?int $navigationSort = -10;

    #[Url]
    public string $activeTab = 'content';

    public function mount(): void
    {
        // Default to content tab if no tab selected
        if (empty($this->activeTab)) {
            $this->activeTab = 'content';
        }
    }

    public function setActiveTab(string $tab): void
    {
        $this->activeTab = $tab;
    }

    public function getContentWidgets(): array
    {
        return [
            ContentStats::class,
        ];
    }

    public function getCrmWidgets(): array
    {
        return [
            CrmStatsOverview::class,
            CrmChart::class,
            LatestLeads::class,
            ProjectStats::class,
            ProjectList::class,
            ProjectDistributionChart::class,
        ];
    }

    public function getWidgets(): array
    {
        return $this->activeTab === 'crm' 
            ? $this->getCrmWidgets() 
            : $this->getContentWidgets();
    }

    public function getVisibleWidgets(): array
    {
        return $this->getWidgets();
    }

    public function getColumns(): int | string | array
    {
        return 2;
    }
}

<?php

namespace App\Filament\Widgets;

use App\Models\Lead;
use Filament\Tables;
use Filament\Tables\Table;
use Filament\Widgets\TableWidget as BaseWidget;

class LatestLeads extends BaseWidget
{
    protected static ?string $heading = 'Leads Terbaru';

    protected static ?int $sort = 3;

    protected int | string | array $columnSpan = 1;

    public static function canView(): bool
    {
        return request()->routeIs('filament.admin.pages.crm-dashboard');
    }

    public function table(Table $table): Table
    {
        return $table
            ->query(
                Lead::query()->latest()->limit(5)
            )
            ->columns([
                Tables\Columns\TextColumn::make('name')
                    ->label('Name'),
                Tables\Columns\TextColumn::make('company')
                    ->label('Company'),
                Tables\Columns\TextColumn::make('status')
                    ->label('Status')
                    ->badge()
                    ->color(fn (string $state): string => match ($state) {
                        'new' => 'info',
                        'in_progress' => 'warning',
                        'negotiation' => 'primary',
                        'deal' => 'success',
                        'rejected' => 'danger',
                        default => 'gray',
                    })
                    ->formatStateUsing(fn (string $state): string => match ($state) {
                        'new' => 'Baru',
                        'in_progress' => 'In Progress',
                        'negotiation' => 'Negotiation',
                        'deal' => 'Deal',
                        'rejected' => 'Ditolak',
                        default => $state,
                    }),
            ])
            ->paginated(false);
    }
}

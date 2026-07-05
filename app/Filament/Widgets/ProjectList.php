<?php

namespace App\Filament\Widgets;

use App\Models\Project;
use Filament\Tables;
use Filament\Tables\Table;
use Filament\Widgets\TableWidget as BaseWidget;
use Carbon\Carbon;

class ProjectList extends BaseWidget
{
    protected static ?string $heading = 'Daftar Proyek';

    protected static ?int $sort = 5;

    protected int | string | array $columnSpan = 1;

    public static function canView(): bool
    {
        return request()->routeIs('filament.admin.pages.crm-dashboard');
    }

    public function table(Table $table): Table
    {
        return $table
            ->query(
                Project::query()->where('status', 'active')->orderBy('deadline')->limit(5)
            )
            ->columns([
                Tables\Columns\TextColumn::make('name')
                    ->label('Nama')
                    ->limit(25),
                Tables\Columns\TextColumn::make('type')
                    ->label('Tipe')
                    ->badge()
                    ->color(fn (string $state): string => match ($state) {
                        'development' => 'info',
                        'marketing' => 'warning',
                        'execution' => 'success',
                        'design' => 'primary',
                        'consulting' => 'gray',
                        default => 'gray',
                    }),
                Tables\Columns\TextColumn::make('deadline')
                    ->label('Hari')
                    ->formatStateUsing(function ($state) {
                        if (!$state) return '-';
                        $days = Carbon::now()->diffInDays(Carbon::parse($state), false);
                        return $days >= 0 ? "{$days} Day" : "Overdue";
                    }),
                Tables\Columns\TextColumn::make('progress')
                    ->label('%')
                    ->suffix('%'),
            ])
            ->paginated(false);
    }
}

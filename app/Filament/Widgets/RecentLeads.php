<?php

namespace App\Filament\Widgets;

use App\Models\Lead;
use Filament\Tables;
use Filament\Tables\Table;
use Filament\Widgets\TableWidget as BaseWidget;

class RecentLeads extends BaseWidget
{
    protected static ?string $heading = 'Lead Terbaru';

    protected static ?int $sort = 5;

    protected int | string | array $columnSpan = 1;

    public function table(Table $table): Table
    {
        return $table
            ->query(
                Lead::query()
                    ->whereNull('converted_to_client_id')
                    ->orderByDesc('created_at')
            )
            ->columns([
                Tables\Columns\TextColumn::make('name')
                    ->label('Nama')
                    ->searchable(),
                Tables\Columns\TextColumn::make('company')
                    ->label('Perusahaan')
                    ->placeholder('-'),
                Tables\Columns\TextColumn::make('status')
                    ->badge()
                    ->color(fn (string $state): string => match ($state) {
                        'new' => 'info',
                        'contacted' => 'warning',
                        'meeting' => 'primary',
                        'proposal' => 'purple',
                        'negotiation' => 'pink',
                        'deal' => 'success',
                        'rejected' => 'danger',
                        default => 'gray',
                    })
                    ->formatStateUsing(fn (string $state): string => Lead::STATUS_OPTIONS[$state] ?? $state),
                Tables\Columns\TextColumn::make('created_at')
                    ->label('Tanggal')
                    ->since(),
            ])
            ->actions([
                Tables\Actions\Action::make('whatsapp')
                    ->icon('heroicon-o-chat-bubble-left-ellipsis')
                    ->color('success')
                    ->url(fn (Lead $record): ?string => $record->whatsapp_url)
                    ->openUrlInNewTab()
                    ->visible(fn (Lead $record): bool => !empty($record->whatsapp)),
                Tables\Actions\Action::make('view')
                    ->icon('heroicon-o-eye')
                    ->url(fn (Lead $record): string => route('filament.admin.resources.leads.view', $record)),
            ])
            ->paginated([5]);
    }
}

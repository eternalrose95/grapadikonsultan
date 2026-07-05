<?php

namespace App\Filament\Widgets;

use App\Models\Interaction;
use App\Models\Lead;
use App\Models\Client;
use Filament\Tables;
use Filament\Tables\Table;
use Filament\Widgets\TableWidget as BaseWidget;

class PendingFollowUps extends BaseWidget
{
    protected static ?string $heading = 'Follow-up Hari Ini';

    protected static ?int $sort = 4;

    protected int | string | array $columnSpan = 1;

    public function table(Table $table): Table
    {
        return $table
            ->query(
                Interaction::query()
                    ->whereNotNull('follow_up_at')
                    ->where('follow_up_completed', false)
                    ->where('follow_up_at', '<=', now()->endOfDay())
                    ->orderBy('follow_up_at')
            )
            ->columns([
                Tables\Columns\TextColumn::make('follow_up_at')
                    ->label('Waktu')
                    ->dateTime('H:i')
                    ->sortable(),
                Tables\Columns\TextColumn::make('interactable')
                    ->label('Kontak')
                    ->formatStateUsing(function (Interaction $record) {
                        if ($record->interactable_type === Lead::class) {
                            return $record->interactable?->name ?? '-';
                        }
                        return $record->interactable?->company_name ?? '-';
                    }),
                Tables\Columns\TextColumn::make('type')
                    ->label('Tipe')
                    ->badge()
                    ->color(fn (string $state): string => match ($state) {
                        'call' => 'info',
                        'whatsapp' => 'success',
                        'email' => 'primary',
                        'meeting' => 'warning',
                        default => 'gray',
                    })
                    ->formatStateUsing(fn (string $state): string => Interaction::TYPE_OPTIONS[$state] ?? $state),
                Tables\Columns\TextColumn::make('subject')
                    ->label('Subject')
                    ->limit(20),
            ])
            ->actions([
                Tables\Actions\Action::make('complete')
                    ->icon('heroicon-o-check-circle')
                    ->color('success')
                    ->action(fn (Interaction $record) => $record->update(['follow_up_completed' => true]))
                    ->requiresConfirmation(),
            ])
            ->paginated([5]);
    }
}

<?php

namespace App\Filament\Resources;

use App\Filament\Resources\InteractionResource\Pages;
use App\Models\Client;
use App\Models\Interaction;
use App\Models\Lead;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;

class InteractionResource extends Resource
{
    protected static ?string $model = Interaction::class;

    protected static ?string $navigationIcon = 'heroicon-o-chat-bubble-left-right';

    protected static ?string $navigationGroup = 'CRM';

    protected static ?int $navigationSort = 4;

    protected static ?string $navigationLabel = 'Interactions';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Section::make('Interaction Details')
                    ->schema([
                        Forms\Components\Select::make('interactable_type')
                            ->label('Tipe Kontak')
                            ->options([
                                Lead::class => 'Lead',
                                Client::class => 'Client',
                            ])
                            ->required()
                            ->reactive(),

                        Forms\Components\Select::make('interactable_id')
                            ->label('Pilih Lead/Client')
                            ->options(function (callable $get) {
                                $type = $get('interactable_type');
                                if ($type === Lead::class) {
                                    return Lead::pluck('name', 'id');
                                } elseif ($type === Client::class) {
                                    return Client::pluck('company_name', 'id');
                                }
                                return [];
                            })
                            ->required()
                            ->searchable(),

                        Forms\Components\Select::make('type')
                            ->label('Tipe Interaksi')
                            ->options(Interaction::TYPE_OPTIONS)
                            ->required(),

                        Forms\Components\TextInput::make('subject')
                            ->label('Subject')
                            ->maxLength(200),
                    ])->columns(2),

                Forms\Components\Section::make('Notes & Outcome')
                    ->schema([
                        Forms\Components\Textarea::make('notes')
                            ->label('Catatan')
                            ->required()
                            ->rows(4)
                            ->columnSpanFull(),

                        Forms\Components\Select::make('outcome')
                            ->label('Hasil')
                            ->options(Interaction::OUTCOME_OPTIONS),

                        Forms\Components\DateTimePicker::make('follow_up_at')
                            ->label('Follow-up Pada'),

                        Forms\Components\Toggle::make('follow_up_completed')
                            ->label('Follow-up Selesai')
                            ->default(false),
                    ])->columns(3),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('created_at')
                    ->label('Tanggal')
                    ->dateTime('d M Y H:i')
                    ->sortable(),
                Tables\Columns\TextColumn::make('interactable.name')
                    ->label('Kontak')
                    ->formatStateUsing(function (Interaction $record) {
                        if ($record->interactable_type === Lead::class) {
                            return $record->interactable?->name ?? '-';
                        }
                        return $record->interactable?->company_name ?? '-';
                    })
                    ->searchable(query: function ($query, string $search) {
                        return $query->where(function ($q) use ($search) {
                            $q->whereHasMorph('interactable', [Lead::class], function ($q) use ($search) {
                                $q->where('name', 'like', "%{$search}%");
                            })->orWhereHasMorph('interactable', [Client::class], function ($q) use ($search) {
                                $q->where('company_name', 'like', "%{$search}%");
                            });
                        });
                    }),
                Tables\Columns\TextColumn::make('interactable_type')
                    ->label('Tipe')
                    ->badge()
                    ->formatStateUsing(fn (string $state): string => $state === Lead::class ? 'Lead' : 'Client')
                    ->color(fn (string $state): string => $state === Lead::class ? 'info' : 'success'),
                Tables\Columns\TextColumn::make('type')
                    ->label('Interaksi')
                    ->badge()
                    ->color(fn (string $state): string => match ($state) {
                        'call' => 'info',
                        'whatsapp' => 'success',
                        'email' => 'primary',
                        'meeting' => 'warning',
                        'video_call' => 'purple',
                        default => 'gray',
                    })
                    ->formatStateUsing(fn (string $state): string => Interaction::TYPE_OPTIONS[$state] ?? $state),
                Tables\Columns\TextColumn::make('subject')
                    ->label('Subject')
                    ->limit(25),
                Tables\Columns\TextColumn::make('outcome')
                    ->label('Hasil')
                    ->badge()
                    ->color(fn (?string $state): string => match ($state) {
                        'positive' => 'success',
                        'neutral' => 'gray',
                        'negative' => 'danger',
                        'no_response' => 'warning',
                        default => 'gray',
                    })
                    ->formatStateUsing(fn (?string $state): string => $state ? Interaction::OUTCOME_OPTIONS[$state] ?? $state : '-'),
                Tables\Columns\TextColumn::make('follow_up_at')
                    ->label('Follow-up')
                    ->dateTime('d M H:i')
                    ->color(fn (Interaction $record): string => 
                        $record->is_follow_up_due ? 'danger' : 'gray'
                    ),
                Tables\Columns\IconColumn::make('follow_up_completed')
                    ->label('Done')
                    ->boolean(),
                Tables\Columns\TextColumn::make('user.name')
                    ->label('By')
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->defaultSort('created_at', 'desc')
            ->filters([
                Tables\Filters\SelectFilter::make('type')
                    ->label('Tipe')
                    ->options(Interaction::TYPE_OPTIONS),
                Tables\Filters\SelectFilter::make('outcome')
                    ->label('Hasil')
                    ->options(Interaction::OUTCOME_OPTIONS),
                Tables\Filters\TernaryFilter::make('follow_up_completed')
                    ->label('Follow-up Status')
                    ->placeholder('All')
                    ->trueLabel('Completed')
                    ->falseLabel('Pending'),
                Tables\Filters\Filter::make('pending_follow_up')
                    ->label('Follow-up Today')
                    ->query(fn ($query) => $query->pendingFollowUp()),
            ])
            ->actions([
                Tables\Actions\Action::make('complete_followup')
                    ->label('Complete F/U')
                    ->icon('heroicon-o-check-circle')
                    ->color('success')
                    ->action(fn (Interaction $record) => $record->update(['follow_up_completed' => true]))
                    ->visible(fn (Interaction $record): bool => 
                        !$record->follow_up_completed && $record->follow_up_at
                    )
                    ->requiresConfirmation(),
                Tables\Actions\ViewAction::make(),
                Tables\Actions\EditAction::make(),
                Tables\Actions\DeleteAction::make(),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make(),
                ]),
            ]);
    }

    public static function getRelations(): array
    {
        return [];
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListInteractions::route('/'),
            'create' => Pages\CreateInteraction::route('/create'),
            'edit' => Pages\EditInteraction::route('/{record}/edit'),
        ];
    }
}

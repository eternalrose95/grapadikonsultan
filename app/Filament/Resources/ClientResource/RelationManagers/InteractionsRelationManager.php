<?php

namespace App\Filament\Resources\ClientResource\RelationManagers;

use App\Models\Interaction;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\RelationManagers\RelationManager;
use Filament\Tables;
use Filament\Tables\Table;

class InteractionsRelationManager extends RelationManager
{
    protected static string $relationship = 'interactions';

    protected static ?string $title = 'Interactions';

    public function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Select::make('type')
                    ->label('Tipe')
                    ->options(Interaction::TYPE_OPTIONS)
                    ->required(),
                Forms\Components\TextInput::make('subject')
                    ->label('Subject')
                    ->maxLength(200),
                Forms\Components\Textarea::make('notes')
                    ->label('Catatan')
                    ->required()
                    ->rows(4)
                    ->columnSpanFull(),
                Forms\Components\Select::make('outcome')
                    ->label('Hasil')
                    ->options(Interaction::OUTCOME_OPTIONS),
                Forms\Components\DateTimePicker::make('follow_up_at')
                    ->label('Follow-up'),
            ]);
    }

    public function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('created_at')
                    ->label('Tanggal')
                    ->dateTime('d M Y H:i')
                    ->sortable(),
                Tables\Columns\TextColumn::make('type')
                    ->label('Tipe')
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
                    ->limit(30),
                Tables\Columns\TextColumn::make('outcome')
                    ->label('Hasil')
                    ->badge()
                    ->color(fn (?string $state): string => match ($state) {
                        'positive' => 'success',
                        'neutral' => 'gray',
                        'negative' => 'danger',
                        'no_response' => 'warning',
                        default => 'gray',
                    }),
                Tables\Columns\IconColumn::make('follow_up_completed')
                    ->label('F/U Done')
                    ->boolean(),
                Tables\Columns\TextColumn::make('user.name')
                    ->label('By'),
            ])
            ->defaultSort('created_at', 'desc')
            ->filters([
                //
            ])
            ->headerActions([
                Tables\Actions\CreateAction::make()
                    ->mutateFormDataUsing(function (array $data): array {
                        $data['user_id'] = auth()->id();
                        return $data;
                    }),
            ])
            ->actions([
                Tables\Actions\EditAction::make(),
                Tables\Actions\DeleteAction::make(),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make(),
                ]),
            ]);
    }
}

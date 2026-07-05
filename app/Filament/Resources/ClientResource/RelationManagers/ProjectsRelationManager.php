<?php

namespace App\Filament\Resources\ClientResource\RelationManagers;

use App\Models\Project;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\RelationManagers\RelationManager;
use Filament\Tables;
use Filament\Tables\Table;

class ProjectsRelationManager extends RelationManager
{
    protected static string $relationship = 'projects';

    protected static ?string $title = 'Projects';

    public function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\TextInput::make('name')
                    ->label('Nama Project')
                    ->required()
                    ->maxLength(200),
                Forms\Components\Select::make('type')
                    ->label('Tipe')
                    ->options(Project::TYPE_OPTIONS)
                    ->required(),
                Forms\Components\DatePicker::make('deadline')
                    ->label('Deadline'),
                Forms\Components\Select::make('status')
                    ->options(Project::STATUS_OPTIONS)
                    ->default('active')
                    ->required(),
                Forms\Components\TextInput::make('budget')
                    ->label('Budget (Rp)')
                    ->numeric()
                    ->prefix('Rp'),
                Forms\Components\TextInput::make('progress')
                    ->label('Progress (%)')
                    ->numeric()
                    ->default(0)
                    ->minValue(0)
                    ->maxValue(100)
                    ->suffix('%'),
            ]);
    }

    public function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('name')
                    ->label('Nama')
                    ->searchable(),
                Tables\Columns\TextColumn::make('type')
                    ->label('Tipe')
                    ->badge()
                    ->color(fn (string $state): string => match ($state) {
                        'development' => 'info',
                        'marketing' => 'warning',
                        'execution' => 'success',
                        'design' => 'primary',
                        'consulting' => 'gray',
                        'research' => 'purple',
                        default => 'gray',
                    }),
                Tables\Columns\TextColumn::make('progress')
                    ->suffix('%'),
                Tables\Columns\TextColumn::make('status')
                    ->badge()
                    ->color(fn (string $state): string => match ($state) {
                        'active' => 'success',
                        'completed' => 'info',
                        'on_hold' => 'warning',
                        'cancelled' => 'danger',
                        default => 'gray',
                    }),
                Tables\Columns\TextColumn::make('deadline')
                    ->date(),
                Tables\Columns\TextColumn::make('budget')
                    ->money('IDR'),
            ])
            ->filters([
                //
            ])
            ->headerActions([
                Tables\Actions\CreateAction::make(),
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

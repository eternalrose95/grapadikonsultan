<?php

namespace App\Filament\Resources;

use App\Filament\Resources\ExecutiveTeamResource\Pages;
use App\Filament\Resources\ExecutiveTeamResource\RelationManagers;
use App\Models\ExecutiveTeam;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;

class ExecutiveTeamResource extends Resource
{
    protected static ?string $model = ExecutiveTeam::class;

    protected static ?string $navigationIcon = 'heroicon-o-users';

    protected static ?string $navigationGroup = 'Konten';

    protected static ?string $navigationLabel = 'Executive Team';

    protected static ?int $navigationSort = 6;

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Section::make('Personal Info')
                    ->schema([
                        Forms\Components\TextInput::make('name')
                            ->required()
                            ->maxLength(100),
                        Forms\Components\TextInput::make('position')
                            ->required()
                            ->maxLength(100),
                        Forms\Components\TextInput::make('linkedin_url')
                            ->label('LinkedIn URL')
                            ->url()
                            ->maxLength(255),
                    ])->columns(2),

                Forms\Components\Section::make('Profile')
                    ->schema([
                        Forms\Components\FileUpload::make('photo_url')
                            ->label('Photo')
                            ->image()
                            ->directory('team')
                            ->imageEditor(),
                        Forms\Components\Textarea::make('bio')
                            ->rows(5)
                            ->columnSpanFull(),
                    ]),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\ImageColumn::make('photo_url')
                    ->label('Photo')
                    ->circular(),
                Tables\Columns\TextColumn::make('name')
                    ->searchable()
                    ->sortable(),
                Tables\Columns\TextColumn::make('position')
                    ->searchable()
                    ->sortable()
                    ->badge()
                    ->color('primary'),
                Tables\Columns\TextColumn::make('linkedin_url')
                    ->label('LinkedIn')
                    ->url(fn ($record) => $record->linkedin_url)
                    ->openUrlInNewTab()
                    ->limit(30)
                    ->toggleable(isToggledHiddenByDefault: true),
                Tables\Columns\TextColumn::make('created_at')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                //
            ])
            ->actions([
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
        return [
            //
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListExecutiveTeams::route('/'),
            'create' => Pages\CreateExecutiveTeam::route('/create'),
            'edit' => Pages\EditExecutiveTeam::route('/{record}/edit'),
        ];
    }
}

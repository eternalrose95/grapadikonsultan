<?php

namespace App\Filament\Resources;

use App\Filament\Resources\PortfolioResource\Pages;
use App\Filament\Resources\PortfolioResource\RelationManagers;
use App\Models\Portfolio;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;

class PortfolioResource extends Resource
{
    protected static ?string $model = Portfolio::class;

    protected static ?string $navigationIcon = 'heroicon-o-folder';

    protected static ?string $navigationGroup = 'Portfolio';

    protected static ?int $navigationSort = 1;

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Section::make('Project Details')
                    ->schema([
                        Forms\Components\TextInput::make('project_title')
                            ->label('Project Title')
                            ->required()
                            ->maxLength(200),
                        Forms\Components\TextInput::make('client_name')
                            ->label('Client Name')
                            ->required()
                            ->maxLength(100),
                        Forms\Components\TextInput::make('location')
                            ->label('Location (City)')
                            ->placeholder('e.g. Jakarta, Surabaya')
                            ->maxLength(100),
                        Forms\Components\TextInput::make('project_year')
                            ->label('Year')
                            ->numeric()
                            ->minValue(1900)
                            ->maxValue(2100),
                        Forms\Components\Select::make('service_id')
                            ->label('Service')
                            ->relationship('service', 'service_name')
                            ->searchable()
                            ->preload(),
                        Forms\Components\Select::make('portfolio_category_id')
                            ->label('Category')
                            ->relationship('category', 'name')
                            ->searchable()
                            ->preload()
                            ->createOptionForm([
                                Forms\Components\TextInput::make('name')
                                    ->required()
                                    ->maxLength(100),
                                Forms\Components\TextInput::make('slug')
                                    ->maxLength(100),
                            ]),
                    ])->columns(2),

                Forms\Components\Section::make('Image')
                    ->schema([
                        Forms\Components\TextInput::make('image_url')
                            ->label('Image URL')
                            ->url()
                            ->placeholder('https://example.com/image.jpg')
                            ->helperText('Enter the URL of the project image'),
                    ]),

                Forms\Components\Section::make('Description')
                    ->schema([
                        Forms\Components\Textarea::make('description')
                            ->rows(5)
                            ->columnSpanFull(),
                    ]),

                Forms\Components\Section::make('Results / Achievements')
                    ->schema([
                        Forms\Components\TagsInput::make('results')
                            ->label('Project Results')
                            ->placeholder('Add a result and press Enter')
                            ->helperText('Add results/achievements that will be displayed as bullet points'),
                    ]),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('project_title')
                    ->label('Project')
                    ->searchable()
                    ->sortable(),
                Tables\Columns\TextColumn::make('client_name')
                    ->label('Client')
                    ->searchable()
                    ->sortable(),
                Tables\Columns\TextColumn::make('location')
                    ->label('Location')
                    ->searchable()
                    ->sortable()
                    ->toggleable(),
                Tables\Columns\TextColumn::make('project_year')
                    ->label('Year')
                    ->sortable(),
                Tables\Columns\TextColumn::make('service.service_name')
                    ->label('Service')
                    ->badge()
                    ->sortable(),
                Tables\Columns\TextColumn::make('category.name')
                    ->label('Category')
                    ->badge()
                    ->color('success')
                    ->sortable(),
                Tables\Columns\TextColumn::make('created_at')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                Tables\Filters\SelectFilter::make('service')
                    ->relationship('service', 'service_name'),
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
            'index' => Pages\ListPortfolios::route('/'),
            'create' => Pages\CreatePortfolio::route('/create'),
            'edit' => Pages\EditPortfolio::route('/{record}/edit'),
        ];
    }
}

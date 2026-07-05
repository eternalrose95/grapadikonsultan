<?php

namespace App\Filament\Resources;

use App\Filament\Resources\CompanyTimelineResource\Pages;
use App\Models\CompanyTimeline;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;

class CompanyTimelineResource extends Resource
{
    protected static ?string $model = CompanyTimeline::class;

    protected static ?string $navigationIcon = 'heroicon-o-clock';

    protected static ?string $navigationGroup = 'Konten';

    protected static ?int $navigationSort = 5;

    protected static ?string $navigationLabel = 'Company Timeline';

    protected static ?string $modelLabel = 'Timeline';

    protected static ?string $pluralModelLabel = 'Timelines';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Section::make('Timeline Event')
                    ->schema([
                        Forms\Components\TextInput::make('year')
                            ->label('Year')
                            ->required()
                            ->maxLength(10)
                            ->placeholder('e.g., 2020'),
                        Forms\Components\TextInput::make('title')
                            ->label('Title')
                            ->required()
                            ->maxLength(200)
                            ->placeholder('e.g., Company Founded'),
                        Forms\Components\Textarea::make('description')
                            ->label('Description')
                            ->required()
                            ->rows(4)
                            ->columnSpanFull(),
                        Forms\Components\FileUpload::make('image')
                            ->label('Image')
                            ->image()
                            ->directory('timelines')
                            ->imageResizeMode('cover')
                            ->imageCropAspectRatio('16:9')
                            ->maxSize(2048)
                            ->columnSpanFull(),
                    ])->columns(2),

                Forms\Components\Section::make('Settings')
                    ->schema([
                        Forms\Components\Toggle::make('is_active')
                            ->label('Active')
                            ->default(true)
                            ->helperText('Show this event on the timeline page'),
                        Forms\Components\TextInput::make('sort_order')
                            ->label('Sort Order')
                            ->numeric()
                            ->default(0)
                            ->helperText('Lower numbers appear first'),
                    ])->columns(2),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('year')
                    ->label('Year')
                    ->searchable()
                    ->sortable()
                    ->badge()
                    ->color('primary'),
                Tables\Columns\TextColumn::make('title')
                    ->label('Title')
                    ->searchable()
                    ->sortable()
                    ->limit(40),
                Tables\Columns\ImageColumn::make('image')
                    ->label('Image')
                    ->circular(false)
                    ->height(40),
                Tables\Columns\IconColumn::make('is_active')
                    ->label('Active')
                    ->boolean(),
                Tables\Columns\TextColumn::make('sort_order')
                    ->label('Order')
                    ->sortable(),
            ])
            ->defaultSort('sort_order', 'asc')
            ->reorderable('sort_order')
            ->filters([
                Tables\Filters\TernaryFilter::make('is_active')
                    ->label('Active Status'),
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

    public static function getRelations(): array
    {
        return [
            //
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListCompanyTimelines::route('/'),
            'create' => Pages\CreateCompanyTimeline::route('/create'),
            'edit' => Pages\EditCompanyTimeline::route('/{record}/edit'),
        ];
    }
}

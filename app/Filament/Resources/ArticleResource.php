<?php

namespace App\Filament\Resources;

use App\Filament\Resources\ArticleResource\Pages;
use App\Filament\Resources\ArticleResource\RelationManagers;
use App\Models\Article;
use App\Models\Category;
use App\Models\Tag;
use App\Models\User;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use Illuminate\Support\Str;

class ArticleResource extends Resource
{
    protected static ?string $model = Article::class;

    protected static ?string $navigationIcon = 'heroicon-o-document-text';

    protected static ?string $navigationGroup = 'Blog';

    protected static ?int $navigationSort = 1;

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Group::make()
                    ->schema([
                        Forms\Components\Section::make('Article Details')
                            ->schema([
                                Forms\Components\TextInput::make('title')
                                    ->required()
                                    ->maxLength(255)
                                    ->live(onBlur: true)
                                    ->afterStateUpdated(fn ($state, Forms\Set $set) => $set('slug', Str::slug($state))),
                                Forms\Components\TextInput::make('slug')
                                    ->required()
                                    ->maxLength(255)
                                    ->unique(ignoreRecord: true),
                                Forms\Components\Select::make('category_id')
                                    ->label('Category')
                                    ->relationship('category', 'category_name')
                                    ->searchable()
                                    ->preload()
                                    ->createOptionForm([
                                        Forms\Components\TextInput::make('category_name')
                                            ->required()
                                            ->maxLength(100),
                                        Forms\Components\TextInput::make('slug')
                                            ->required()
                                            ->maxLength(100),
                                    ]),
                                Forms\Components\Select::make('author_id')
                                    ->label('Author')
                                    ->relationship('author', 'name')
                                    ->searchable()
                                    ->preload()
                                    ->default(auth()->id()),
                                Forms\Components\Select::make('tags')
                                    ->label('Tags')
                                    ->relationship('tags', 'name')
                                    ->multiple()
                                    ->searchable()
                                    ->preload()
                                    ->createOptionForm([
                                        Forms\Components\TextInput::make('name')
                                            ->required()
                                            ->maxLength(100)
                                            ->live(onBlur: true)
                                            ->afterStateUpdated(fn ($state, Forms\Set $set) => $set('slug', Str::slug($state))),
                                        Forms\Components\TextInput::make('slug')
                                            ->required()
                                            ->maxLength(100),
                                    ]),
                            ])->columns(2),

                        Forms\Components\Section::make('Content')
                            ->schema([
                                Forms\Components\FileUpload::make('image_url')
                                    ->label('Featured Image')
                                    ->image()
                                    ->directory('articles')
                                    ->columnSpanFull(),
                                Forms\Components\Textarea::make('excerpt')
                                    ->label('Excerpt / Summary')
                                    ->helperText('Brief summary of the article. If empty, will be auto-generated from content.')
                                    ->rows(3)
                                    ->maxLength(500)
                                    ->columnSpanFull(),
                                Forms\Components\RichEditor::make('content')
                                    ->required()
                                    ->columnSpanFull(),
                            ]),
                    ])->columnSpan(['lg' => 2]),

                Forms\Components\Group::make()
                    ->schema([
                        Forms\Components\Section::make('Publishing')
                            ->schema([
                                Forms\Components\Toggle::make('is_published')
                                    ->label('Published')
                                    ->default(true)
                                    ->helperText('Unpublished articles are hidden from the blog.'),
                                Forms\Components\Toggle::make('is_featured')
                                    ->label('Featured')
                                    ->helperText('Featured articles appear prominently on the blog page.'),
                                Forms\Components\DateTimePicker::make('published_at')
                                    ->label('Publish Date')
                                    ->helperText('Leave empty to use creation date.'),
                                Forms\Components\TextInput::make('reading_time')
                                    ->label('Reading Time (minutes)')
                                    ->numeric()
                                    ->minValue(1)
                                    ->helperText('Auto-calculated if left empty.'),
                            ]),

                        Forms\Components\Section::make('SEO')
                            ->schema([
                                Forms\Components\TextInput::make('meta_title')
                                    ->label('Meta Title')
                                    ->maxLength(255)
                                    ->helperText('SEO title. Defaults to article title.'),
                                Forms\Components\Textarea::make('meta_description')
                                    ->label('Meta Description')
                                    ->rows(3)
                                    ->maxLength(300)
                                    ->helperText('SEO description. Defaults to excerpt.'),
                            ]),

                        Forms\Components\Section::make('Stats')
                            ->schema([
                                Forms\Components\Placeholder::make('views_count_display')
                                    ->label('Views')
                                    ->content(fn (?Article $record): string => $record ? number_format($record->views_count) : '0'),
                                Forms\Components\Placeholder::make('created_at_display')
                                    ->label('Created')
                                    ->content(fn (?Article $record): string => $record ? $record->created_at->diffForHumans() : '-'),
                                Forms\Components\Placeholder::make('updated_at_display')
                                    ->label('Last Updated')
                                    ->content(fn (?Article $record): string => $record ? $record->updated_at->diffForHumans() : '-'),
                            ])
                            ->visibleOn('edit'),
                    ])->columnSpan(['lg' => 1]),
            ])->columns(3);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\ImageColumn::make('image_url')
                    ->label('Image')
                    ->circular(),
                Tables\Columns\TextColumn::make('title')
                    ->searchable()
                    ->sortable()
                    ->limit(50),
                Tables\Columns\TextColumn::make('category.category_name')
                    ->label('Category')
                    ->sortable()
                    ->badge(),
                Tables\Columns\TextColumn::make('author.name')
                    ->label('Author')
                    ->sortable(),
                Tables\Columns\IconColumn::make('is_published')
                    ->label('Published')
                    ->boolean(),
                Tables\Columns\IconColumn::make('is_featured')
                    ->label('Featured')
                    ->boolean(),
                Tables\Columns\TextColumn::make('views_count')
                    ->label('Views')
                    ->sortable()
                    ->numeric(),
                Tables\Columns\TextColumn::make('reading_time')
                    ->label('Read Time')
                    ->suffix(' min')
                    ->toggleable(isToggledHiddenByDefault: true),
                Tables\Columns\TextColumn::make('created_at')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                Tables\Filters\SelectFilter::make('category')
                    ->relationship('category', 'category_name'),
                Tables\Filters\SelectFilter::make('author')
                    ->relationship('author', 'name'),
                Tables\Filters\TernaryFilter::make('is_published')
                    ->label('Published'),
                Tables\Filters\TernaryFilter::make('is_featured')
                    ->label('Featured'),
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
            ])
            ->defaultSort('created_at', 'desc');
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
            'index' => Pages\ListArticles::route('/'),
            'create' => Pages\CreateArticle::route('/create'),
            'edit' => Pages\EditArticle::route('/{record}/edit'),
        ];
    }
}

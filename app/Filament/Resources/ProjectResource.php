<?php

namespace App\Filament\Resources;

use App\Filament\Resources\ProjectResource\Pages;
use App\Models\Client;
use App\Models\Project;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Infolists\Components\Tabs;
use Filament\Infolists\Components\TextEntry;
use Filament\Infolists\Infolist;
use Filament\Tables\Table;
use Illuminate\Support\Facades\Storage;
use Joaopaulolndev\FilamentPdfViewer\Infolists\Components\PdfViewerEntry;
use RyanChandler\FilamentProgressColumn\ProgressColumn;

class ProjectResource extends Resource
{
    protected static ?string $model = Project::class;

    protected static ?string $navigationIcon = 'heroicon-o-clipboard-document-list';

    protected static ?string $navigationGroup = 'CRM';

    protected static ?int $navigationSort = 5;

    protected static ?string $navigationLabel = 'Proyek';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Section::make('Project Information')
                    ->schema([
                        Forms\Components\Select::make('client_id')
                            ->label('Client')
                            ->relationship('client', 'company_name')
                            ->searchable()
                            ->preload()
                            ->createOptionForm([
                                Forms\Components\TextInput::make('company_name')
                                    ->required()
                                    ->maxLength(200),
                                Forms\Components\TextInput::make('pic_name')
                                    ->required()
                                    ->maxLength(100),
                            ]),
                        Forms\Components\TextInput::make('name')
                            ->label('Nama Proyek')
                            ->required()
                            ->maxLength(200),
                        Forms\Components\Select::make('type')
                            ->label('Tipe')
                            ->options(Project::TYPE_OPTIONS)
                            ->default('development')
                            ->required(),
                        Forms\Components\DatePicker::make('deadline')
                            ->label('Deadline'),
                        Forms\Components\Select::make('status')
                            ->options(Project::STATUS_OPTIONS)
                            ->default('active')
                            ->required(),
                    ])->columns(2),

                Forms\Components\Section::make('Progress & Budget')
                    ->schema([
                        Forms\Components\TextInput::make('progress')
                            ->label('Progress (%)')
                            ->numeric()
                            ->default(0)
                            ->minValue(0)
                            ->maxValue(100)
                            ->columnSpanFull(),
                        Forms\Components\TextInput::make('budget')
                            ->label('Budget (Rp)')
                            ->numeric()
                            ->prefix('Rp')
                            ->columnSpanFull(),
                    ])->columns(2),

                Forms\Components\Section::make('Description')
                    ->schema([
                        Forms\Components\Textarea::make('description')
                            ->label('Deskripsi')
                            ->rows(3)
                            ->columnSpanFull(),
                    ]),

                Forms\Components\Section::make('Contract')
                    ->schema([
                        Forms\Components\FileUpload::make('contract_file')
                            ->label('File Kontrak')
                            ->disk('local')
                            ->directory('contracts')
                            ->visibility('private')
                            ->acceptedFileTypes(['application/pdf', 'application/msword', 'application/vnd.openxmlformats-officedocument.wordprocessingml.document', 'application/vnd.ms-excel', 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet', 'text/csv'])
                            ->validationMessages([
                                'accepted_file_types' => 'Hanya file PDF, Word, Excel, dan CSV yang diperbolehkan.',
                            ])
                            ->multiple()
                            ->reorderable()
                            ->appendFiles()
                            ->downloadable()
                            ->openable()
                            ->columnSpanFull(),
                    ]),

                Forms\Components\Section::make('Report')
                    ->schema([
                        Forms\Components\FileUpload::make('report_file')
                            ->label('File Laporan')
                            ->disk('local')
                            ->directory('reports')
                            ->visibility('private')
                            ->acceptedFileTypes(['application/pdf', 'application/msword', 'application/vnd.openxmlformats-officedocument.wordprocessingml.document', 'application/vnd.ms-excel', 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet', 'text/csv'])
                            ->validationMessages([
                                'accepted_file_types' => 'Hanya file PDF, Word, Excel, dan CSV yang diperbolehkan.',
                            ])
                            ->multiple()
                            ->reorderable()
                            ->appendFiles()
                            ->downloadable()
                            ->openable()
                            ->columnSpanFull(),
                    ]),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('client.company_name')
                    ->label('Client')
                    ->searchable()
                    ->sortable()
                    ->placeholder('No Client'),
                Tables\Columns\TextColumn::make('name')
                    ->label('Nama Proyek')
                    ->searchable()
                    ->sortable(),
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
                    })
                    ->formatStateUsing(fn (string $state): string => Project::TYPE_OPTIONS[$state] ?? $state),
                Tables\Columns\TextColumn::make('deadline')
                    ->date()
                    ->sortable()
                    ->color(fn (Project $record): string => $record->is_overdue ? 'danger' : 'gray'),
                ProgressColumn::make('progress')
                    ->label('Progress')
                    ->color(fn (string $state): string => match (true) {
                        $state >= 80 => 'success',
                        $state >= 50 => 'warning',
                        default => 'gray',
                    })
                    ->sortable(),
                Tables\Columns\TextColumn::make('status')
                    ->badge()
                    ->color(fn (string $state): string => match ($state) {
                        'active' => 'success',
                        'completed' => 'info',
                        'on_hold' => 'warning',
                        'cancelled' => 'danger',
                        default => 'gray',
                    })
                    ->formatStateUsing(fn (string $state): string => Project::STATUS_OPTIONS[$state] ?? $state),
                Tables\Columns\TextColumn::make('budget')
                    ->money('IDR')
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                Tables\Filters\SelectFilter::make('client_id')
                    ->label('Client')
                    ->relationship('client', 'company_name'),
                Tables\Filters\SelectFilter::make('type')
                    ->options(Project::TYPE_OPTIONS),
                Tables\Filters\SelectFilter::make('status')
                    ->options(Project::STATUS_OPTIONS),
                Tables\Filters\Filter::make('overdue')
                    ->label('Overdue')
                    ->query(fn ($query) => $query->where('deadline', '<', now())->where('status', '!=', 'completed')),
            ])
            ->actions([
                Tables\Actions\ViewAction::make(),
                Tables\Actions\EditAction::make(),
                Tables\Actions\ActionGroup::make([
                    Tables\Actions\Action::make('preview_contract')
                        ->label('Lihat Kontrak')
                        ->icon('heroicon-o-document-text')
                        ->color('gray')
                        ->visible(fn (Project $record): bool => !empty($record->contract_file))
                        ->modalWidth('7xl')
                        ->modalSubmitAction(false)
                        ->modalCancelAction(false)
                        ->infolist(function (Infolist $infolist) {
                            return $infolist->schema([
                                Tabs::make('Contracts')
                                    ->tabs(function (Project $record) {
                                        $files = $record->contract_file;
                                        if (is_string($files)) $files = [$files];
                                        return collect($files)->map(function ($file, $index) use ($record) {
                                            $ext = strtolower(pathinfo($file, PATHINFO_EXTENSION));
                                            $tab = Tabs\Tab::make('Dokumen ' . ($index + 1));
                                            if ($ext === 'pdf') {
                                                return $tab->schema([
                                                    PdfViewerEntry::make('pdf_viewer_' . $index)
                                                        ->label('')
                                                        ->minHeight('80svh')
                                                        ->fileUrl(route('projects.contract', ['project' => $record, 'file_index' => $index, 'stream' => 1]))
                                                ]);
                                            }
                                            return $tab->schema([
                                                TextEntry::make('download_' . $index)
                                                    ->label('')
                                                    ->default(basename($file))
                                                    ->formatStateUsing(fn () => '<a href="' . route('projects.contract', ['project' => $record, 'file_index' => $index]) . '" class="inline-flex items-center px-4 py-2 bg-gray-800 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-gray-700 active:bg-gray-900 focus:outline-none focus:border-gray-900 focus:ring ring-gray-300 disabled:opacity-25 transition ease-in-out duration-150">Download ' . basename($file) . '</a>') 
                                                    ->html(),
                                            ]);
                                        })->toArray();
                                    })
                            ]);
                        }),
                    Tables\Actions\Action::make('preview_report')
                        ->label('Lihat Laporan')
                        ->icon('heroicon-o-document-chart-bar')
                        ->color('success')
                        ->visible(fn (Project $record): bool => !empty($record->report_file))
                        ->modalWidth('7xl')
                        ->modalSubmitAction(false)
                        ->modalCancelAction(false)
                        ->infolist(function (Infolist $infolist) {
                            return $infolist->schema([
                                Tabs::make('Reports')
                                    ->tabs(function (Project $record) {
                                        $files = $record->report_file;
                                        if (is_string($files)) $files = [$files];
                                        return collect($files)->map(function ($file, $index) use ($record) {
                                            $ext = strtolower(pathinfo($file, PATHINFO_EXTENSION));
                                            $tab = Tabs\Tab::make('Dokumen ' . ($index + 1));
                                            if ($ext === 'pdf') {
                                                return $tab->schema([
                                                    PdfViewerEntry::make('pdf_viewer_' . $index)
                                                        ->label('')
                                                        ->minHeight('80svh')
                                                        ->fileUrl(route('projects.report', ['project' => $record, 'file_index' => $index, 'stream' => 1]))
                                                ]);
                                            }
                                            return $tab->schema([
                                                TextEntry::make('download_' . $index)
                                                    ->label('')
                                                    ->default(basename($file))
                                                    ->formatStateUsing(fn () => '<a href="' . route('projects.report', ['project' => $record, 'file_index' => $index]) . '" class="inline-flex items-center px-4 py-2 bg-gray-800 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-gray-700 active:bg-gray-900 focus:outline-none focus:border-gray-900 focus:ring ring-gray-300 disabled:opacity-25 transition ease-in-out duration-150">Download ' . basename($file) . '</a>') 
                                                    ->html(),
                                            ]);
                                        })->toArray();
                                    })
                            ]);
                        }),

                ])
                ->label('Dokumen')
                ->icon('heroicon-m-document-duplicate')
                ->color('info'),
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
            'index' => Pages\ListProjects::route('/'),
            'create' => Pages\CreateProject::route('/create'),
            'edit' => Pages\EditProject::route('/{record}/edit'),
        ];
    }
}

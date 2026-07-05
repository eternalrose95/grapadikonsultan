<?php

namespace App\Filament\Resources;

use App\Filament\Resources\ClientResource\Pages;
use App\Filament\Resources\ClientResource\RelationManagers;
use App\Models\Client;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Filament\Infolists;
use Filament\Infolists\Infolist;

class ClientResource extends Resource
{
    protected static ?string $model = Client::class;

    protected static ?string $navigationIcon = 'heroicon-o-building-office-2';

    protected static ?string $navigationGroup = 'CRM';

    protected static ?int $navigationSort = 3;

    protected static ?string $navigationLabel = 'Clients';

    protected static ?string $modelLabel = 'Client';

    protected static ?string $pluralModelLabel = 'Clients';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Section::make('Company Information')
                    ->schema([
                        Forms\Components\TextInput::make('company_name')
                            ->label('Nama Perusahaan')
                            ->required()
                            ->maxLength(200),
                        Forms\Components\Select::make('industry')
                            ->label('Industri')
                            ->options(Client::INDUSTRY_OPTIONS)
                            ->searchable(),
                        Forms\Components\TextInput::make('website')
                            ->label('Website')
                            ->url()
                            ->maxLength(255),
                        Forms\Components\Select::make('status')
                            ->options(Client::STATUS_OPTIONS)
                            ->default('active')
                            ->required(),
                    ])->columns(2),

                Forms\Components\Section::make('Primary Contact (PIC)')
                    ->schema([
                        Forms\Components\TextInput::make('pic_name')
                            ->label('Nama PIC')
                            ->required()
                            ->maxLength(100),
                        Forms\Components\TextInput::make('pic_position')
                            ->label('Jabatan')
                            ->maxLength(100),
                        Forms\Components\TextInput::make('pic_phone')
                            ->label('No. Telepon')
                            ->tel()
                            ->maxLength(20),
                        Forms\Components\TextInput::make('pic_email')
                            ->label('Email')
                            ->email()
                            ->maxLength(100),
                    ])->columns(2),

                Forms\Components\Section::make('Contract Information')
                    ->schema([
                        Forms\Components\TextInput::make('contract_value')
                            ->label('Nilai Kontrak (Rp)')
                            ->numeric()
                            ->prefix('Rp'),
                        Forms\Components\DatePicker::make('contract_start')
                            ->label('Tanggal Mulai'),
                        Forms\Components\DatePicker::make('contract_end')
                            ->label('Tanggal Selesai'),
                    ])->columns(3),

                Forms\Components\Section::make('Additional Info')
                    ->schema([
                        Forms\Components\Textarea::make('address')
                            ->label('Alamat')
                            ->rows(2)
                            ->columnSpanFull(),
                        Forms\Components\Textarea::make('notes')
                            ->label('Catatan')
                            ->rows(3)
                            ->columnSpanFull(),
                    ]),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('company_name')
                    ->label('Perusahaan')
                    ->searchable()
                    ->sortable(),
                Tables\Columns\TextColumn::make('pic_name')
                    ->label('PIC')
                    ->searchable(),
                Tables\Columns\TextColumn::make('industry')
                    ->label('Industri')
                    ->badge()
                    ->formatStateUsing(fn (?string $state): string => Client::INDUSTRY_OPTIONS[$state] ?? $state ?? '-'),
                Tables\Columns\TextColumn::make('status')
                    ->badge()
                    ->color(fn (string $state): string => match ($state) {
                        'active' => 'success',
                        'inactive' => 'warning',
                        'churned' => 'danger',
                        default => 'gray',
                    })
                    ->formatStateUsing(fn (string $state): string => Client::STATUS_OPTIONS[$state] ?? $state),
                Tables\Columns\TextColumn::make('contract_value')
                    ->label('Nilai Kontrak')
                    ->money('IDR')
                    ->sortable(),
                Tables\Columns\TextColumn::make('projects_count')
                    ->label('Projects')
                    ->counts('projects')
                    ->badge()
                    ->color('info'),
                Tables\Columns\TextColumn::make('created_at')
                    ->label('Tanggal')
                    ->date('d M Y')
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->defaultSort('created_at', 'desc')
            ->filters([
                Tables\Filters\SelectFilter::make('status')
                    ->options(Client::STATUS_OPTIONS),
                Tables\Filters\SelectFilter::make('industry')
                    ->label('Industri')
                    ->options(Client::INDUSTRY_OPTIONS),
            ])
            ->actions([
                Tables\Actions\Action::make('whatsapp')
                    ->label('Chat WA')
                    ->icon('heroicon-o-chat-bubble-left-ellipsis')
                    ->color('success')
                    ->url(fn (Client $record): ?string => $record->pic_phone ? "https://wa.me/62" . ltrim(preg_replace('/[^0-9]/', '', $record->pic_phone), '0') : null)
                    ->openUrlInNewTab()
                    ->visible(fn (Client $record): bool => !empty($record->pic_phone)),
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
            RelationManagers\ProjectsRelationManager::class,
            RelationManagers\InteractionsRelationManager::class,
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListClients::route('/'),
            'create' => Pages\CreateClient::route('/create'),
            'view' => Pages\ViewClient::route('/{record}'),
            'edit' => Pages\EditClient::route('/{record}/edit'),
        ];
    }
}

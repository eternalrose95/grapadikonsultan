<?php

namespace App\Filament\Resources\ExecutiveTeamResource\Pages;

use App\Filament\Resources\ExecutiveTeamResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditExecutiveTeam extends EditRecord
{
    protected static string $resource = ExecutiveTeamResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\DeleteAction::make(),
        ];
    }
}

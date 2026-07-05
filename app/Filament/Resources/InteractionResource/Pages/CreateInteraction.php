<?php

namespace App\Filament\Resources\InteractionResource\Pages;

use App\Filament\Resources\InteractionResource;
use Filament\Resources\Pages\CreateRecord;

class CreateInteraction extends CreateRecord
{
    protected static string $resource = InteractionResource::class;

    protected function mutateFormDataBeforeCreate(array $data): array
    {
        $data['user_id'] = auth()->id();
        return $data;
    }
}

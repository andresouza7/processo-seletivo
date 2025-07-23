<?php

namespace App\Filament\Gps\Resources\InscricaoResource\Pages;

use App\Filament\Gps\Resources\InscricaoResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditInscricao extends EditRecord
{
    protected static string $resource = InscricaoResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\ViewAction::make(),
            // Actions\DeleteAction::make(),
        ];
    }
}

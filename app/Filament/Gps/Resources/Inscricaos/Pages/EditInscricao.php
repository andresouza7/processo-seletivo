<?php

namespace App\Filament\Gps\Resources\Inscricaos\Pages;

use Filament\Actions\ViewAction;
use App\Filament\Gps\Resources\Inscricaos\InscricaoResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditInscricao extends EditRecord
{
    protected static string $resource = InscricaoResource::class;

    protected function getHeaderActions(): array
    {
        return [
            ViewAction::make(),
            // Actions\DeleteAction::make(),
        ];
    }
}

<?php

namespace App\Filament\Gps\Resources\Inscricaos\Pages;

use Filament\Actions\EditAction;
use App\Filament\Gps\Resources\Inscricaos\InscricaoResource;
use Filament\Actions;
use Filament\Resources\Pages\ViewRecord;

class ViewInscricao extends ViewRecord
{
    protected static string $resource = InscricaoResource::class;

    protected function getHeaderActions(): array
    {
        return [
            EditAction::make(),
        ];
    }
}

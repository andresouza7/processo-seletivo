<?php

namespace App\Filament\Gps\Resources\InscricaoResource\Pages;

use App\Filament\Gps\Resources\InscricaoResource;
use Filament\Actions;
use Filament\Resources\Pages\ViewRecord;

class ViewInscricao extends ViewRecord
{
    protected static string $resource = InscricaoResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\EditAction::make(),
        ];
    }
}

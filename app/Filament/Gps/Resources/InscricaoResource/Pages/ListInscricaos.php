<?php

namespace App\Filament\Gps\Resources\InscricaoResource\Pages;

use App\Filament\Gps\Resources\InscricaoResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

class ListInscricaos extends ListRecords
{
    protected static string $resource = InscricaoResource::class;

    protected function getHeaderActions(): array
    {
        return [
            // Actions\CreateAction::make(),
        ];
    }
}

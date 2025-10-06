<?php

namespace App\Filament\Gps\Resources\Inscricaos\Pages;

use App\Filament\Gps\Resources\Inscricaos\InscricaoResource;
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

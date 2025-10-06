<?php

namespace App\Filament\Candidato\Resources\EtapaRecursos\Pages;

use App\Filament\Candidato\Resources\EtapaRecursos\EtapaRecursoResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

class ListEtapaRecursos extends ListRecords
{
    protected static string $resource = EtapaRecursoResource::class;

    protected function getHeaderActions(): array
    {
        return [
            // Actions\CreateAction::make(),
        ];
    }
}

<?php

namespace App\Filament\Candidato\Resources\Inscricaos\Pages;

use Filament\Actions\CreateAction;
use App\Filament\Candidato\Resources\Inscricaos\InscricaoResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

class ListInscricaos extends ListRecords
{
    protected static string $resource = InscricaoResource::class;

    protected function getHeaderActions(): array
    {
        return [
            CreateAction::make()
                ->label('Nova Inscrição'),
        ];
    }
}

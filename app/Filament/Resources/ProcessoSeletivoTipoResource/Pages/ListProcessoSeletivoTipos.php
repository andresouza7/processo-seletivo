<?php

namespace App\Filament\Resources\ProcessoSeletivoTipoResource\Pages;

use App\Filament\Resources\ProcessoSeletivoTipoResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

class ListProcessoSeletivoTipos extends ListRecords
{
    protected static string $resource = ProcessoSeletivoTipoResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make(),
        ];
    }
}

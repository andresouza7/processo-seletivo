<?php

namespace App\Filament\Resources\ProcessoSeletivoTipos\Pages;

use Filament\Actions\CreateAction;
use App\Filament\Resources\ProcessoSeletivoTipos\ProcessoSeletivoTipoResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

class ListProcessoSeletivoTipos extends ListRecords
{
    protected static string $resource = ProcessoSeletivoTipoResource::class;

    protected function getHeaderActions(): array
    {
        return [
            CreateAction::make(),
        ];
    }
}

<?php

namespace App\Filament\App\Resources\ProcessoSeletivoResource\Pages;

use App\Filament\App\Resources\ProcessoSeletivoResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

class ListProcessoSeletivos extends ListRecords
{
    protected static string $resource = ProcessoSeletivoResource::class;

    protected function getHeaderActions(): array
    {
        return [
            // Actions\CreateAction::make(),
        ];
    }
}

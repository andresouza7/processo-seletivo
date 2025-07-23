<?php

namespace App\Filament\Gps\Resources\ProcessoSeletivoResource\Pages;

use App\Filament\Gps\Resources\ProcessoSeletivoResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

class ListProcessoSeletivos extends ListRecords
{
    protected static string $resource = ProcessoSeletivoResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make(),
        ];
    }
}

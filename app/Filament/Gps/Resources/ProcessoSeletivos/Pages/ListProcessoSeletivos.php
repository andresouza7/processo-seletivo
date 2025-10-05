<?php

namespace App\Filament\Gps\Resources\ProcessoSeletivos\Pages;

use Filament\Actions\CreateAction;
use App\Filament\Gps\Resources\ProcessoSeletivos\ProcessoSeletivoResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

class ListProcessoSeletivos extends ListRecords
{
    protected static string $resource = ProcessoSeletivoResource::class;

    protected function getHeaderActions(): array
    {
        return [
            CreateAction::make(),
        ];
    }
}

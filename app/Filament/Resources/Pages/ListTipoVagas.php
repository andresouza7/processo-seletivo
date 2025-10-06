<?php

namespace App\Filament\Resources\TipoVagaResource\Pages;

use Filament\Actions\CreateAction;
use App\Filament\Resources\TipoVagaResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

class ListTipoVagas extends ListRecords
{
    protected static string $resource = TipoVagaResource::class;

    protected function getHeaderActions(): array
    {
        return [
            CreateAction::make(),
        ];
    }
}

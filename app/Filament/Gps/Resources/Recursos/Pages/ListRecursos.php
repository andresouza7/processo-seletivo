<?php

namespace App\Filament\Gps\Resources\Recursos\Pages;

use Filament\Actions\CreateAction;
use App\Filament\Gps\Resources\Recursos\RecursoResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

class ListRecursos extends ListRecords
{
    protected static string $resource = RecursoResource::class;

    protected function getHeaderActions(): array
    {
        return [
            CreateAction::make(),
        ];
    }
}

<?php

namespace App\Filament\Gps\Resources\RecursoResource\Pages;

use App\Filament\Gps\Resources\RecursoResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

class ListRecursos extends ListRecords
{
    protected static string $resource = RecursoResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make(),
        ];
    }
}

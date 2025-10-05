<?php

namespace App\Filament\Gps\Resources\RecursoResource\Pages;

use Filament\Actions\EditAction;
use App\Filament\Gps\Resources\RecursoResource;
use Filament\Actions;
use Filament\Resources\Pages\ViewRecord;

class ViewRecurso extends ViewRecord
{
    protected static string $resource = RecursoResource::class;

    protected function getHeaderActions(): array
    {
        return [
            EditAction::make(),
        ];
    }
}

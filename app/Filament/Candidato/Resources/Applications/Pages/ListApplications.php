<?php

namespace App\Filament\Candidato\Resources\Applications\Pages;

use Filament\Actions\CreateAction;
use App\Filament\Candidato\Resources\Applications\ApplicationResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

class ListApplications extends ListRecords
{
    protected static string $resource = ApplicationResource::class;

    protected function getHeaderActions(): array
    {
        return [
            CreateAction::make()
                ->label('Nova Inscrição'),
        ];
    }
}

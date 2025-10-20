<?php

namespace App\Filament\Candidato\Resources\Applications\Pages;

use App\Filament\Candidato\Resources\Applications\ApplicationResource;
use Filament\Actions;
use Filament\Resources\Pages\ViewRecord;

class ViewApplication extends ViewRecord
{
    protected static string $resource = ApplicationResource::class;

    protected function getHeaderActions(): array
    {
        return [
            // Actions\EditAction::make(),
        ];
    }
}

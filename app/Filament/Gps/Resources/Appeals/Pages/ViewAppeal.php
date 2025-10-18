<?php

namespace App\Filament\Gps\Resources\Appeals\Pages;

use Filament\Actions\EditAction;
use App\Filament\Gps\Resources\Appeals\AppealResource;
use Filament\Actions;
use Filament\Resources\Pages\ViewRecord;

class ViewAppeal extends ViewRecord
{
    protected static string $resource = AppealResource::class;

    protected function getHeaderActions(): array
    {
        return [
            EditAction::make(),
        ];
    }
}

<?php

namespace App\Filament\Gps\Resources\Processes\Pages;

use App\Filament\Gps\Resources\Processes\ProcessResource;
use Filament\Actions\EditAction;
use Filament\Resources\Pages\ViewRecord;

class ViewProcess extends ViewRecord
{
    protected static string $resource = ProcessResource::class;

    public function getTitle(): string
    {
        return 'Consultar Processo';
    }

    protected function getHeaderActions(): array
    {
        return [
            // EditAction::make(),
        ];
    }
}

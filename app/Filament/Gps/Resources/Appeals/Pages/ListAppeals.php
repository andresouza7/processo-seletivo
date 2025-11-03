<?php

namespace App\Filament\Gps\Resources\Appeals\Pages;

use Filament\Actions\CreateAction;
use App\Filament\Gps\Resources\Appeals\AppealResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

class ListAppeals extends ListRecords
{
    protected static string $resource = AppealResource::class;

    protected function getHeaderActions(): array
    {
        return [
            // CreateAction::make(),
        ];
    }
}

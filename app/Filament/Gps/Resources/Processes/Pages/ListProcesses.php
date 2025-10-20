<?php

namespace App\Filament\Gps\Resources\Processes\Pages;

use Filament\Actions\CreateAction;
use App\Filament\Gps\Resources\Processes\ProcessResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

class ListProcesses extends ListRecords
{
    protected static string $resource = ProcessResource::class;

    protected function getHeaderActions(): array
    {
        return [
            CreateAction::make(),
        ];
    }
}

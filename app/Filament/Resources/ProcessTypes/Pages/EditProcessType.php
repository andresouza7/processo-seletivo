<?php

namespace App\Filament\Resources\ProcessTypes\Pages;

use App\Filament\Resources\ProcessTypes\ProcessTypeResource;
use Filament\Actions\DeleteAction;
use Filament\Actions\ForceDeleteAction;
use Filament\Actions\RestoreAction;
use Filament\Resources\Pages\EditRecord;

class EditProcessType extends EditRecord
{
    protected static string $resource = ProcessTypeResource::class;

    protected function getHeaderActions(): array
    {
        return [
            DeleteAction::make(),
            ForceDeleteAction::make(),
            RestoreAction::make(),
        ];
    }
}

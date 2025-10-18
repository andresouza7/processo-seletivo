<?php

namespace App\Filament\Candidato\Resources\Applications\Pages;

use Filament\Actions\ViewAction;
use App\Filament\Candidato\Resources\Applications\ApplicationResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditApplication extends EditRecord
{
    protected static string $resource = ApplicationResource::class;

    protected function getHeaderActions(): array
    {
        return [
            ViewAction::make(),
            // Actions\DeleteAction::make(),
        ];
    }
}

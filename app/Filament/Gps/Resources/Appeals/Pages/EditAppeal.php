<?php

namespace App\Filament\Gps\Resources\Appeals\Pages;

use Filament\Actions\ViewAction;
use Filament\Actions\DeleteAction;
use App\Filament\Gps\Resources\Appeals\AppealResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditAppeal extends EditRecord
{
    protected static string $resource = AppealResource::class;
    protected static ?string $title = 'Responder Recurso';
    protected static ?string $breadcrumb = 'Responder';

    protected function getHeaderActions(): array
    {
        return [
            ViewAction::make(),
        ];
    }

    protected function mutateFormDataBeforeSave(array $data): array
    {
        $data['evaluated_at'] = now();

        return $data;
    }
}

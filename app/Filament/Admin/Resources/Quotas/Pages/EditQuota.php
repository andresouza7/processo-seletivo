<?php

namespace App\Filament\Admin\Resources\Quotas\Pages;

use App\Filament\Admin\Resources\Quotas\QuotaResource;
use Filament\Actions\DeleteAction;
use Filament\Resources\Pages\EditRecord;

class EditQuota extends EditRecord
{
    protected static string $resource = QuotaResource::class;

    protected function getHeaderActions(): array
    {
        return [
            DeleteAction::make(),
        ];
    }
}

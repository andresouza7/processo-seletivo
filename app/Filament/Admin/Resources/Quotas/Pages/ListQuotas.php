<?php

namespace App\Filament\Admin\Resources\Quotas\Pages;

use App\Filament\Admin\Resources\Quotas\QuotaResource;
use Filament\Actions\CreateAction;
use Filament\Resources\Pages\ListRecords;

class ListQuotas extends ListRecords
{
    protected static string $resource = QuotaResource::class;

    protected function getHeaderActions(): array
    {
        return [
            CreateAction::make(),
        ];
    }
}

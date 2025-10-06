<?php

namespace App\Filament\Gps\Resources\InscricaoPessoas\Pages;

use App\Filament\Gps\Resources\InscricaoPessoas\InscricaoPessoaResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

class ListInscricaoPessoas extends ListRecords
{
    protected static string $resource = InscricaoPessoaResource::class;

    protected function getHeaderActions(): array
    {
        return [
            // Actions\CreateAction::make(),
        ];
    }
}

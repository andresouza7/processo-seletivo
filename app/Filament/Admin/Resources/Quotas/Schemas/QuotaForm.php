<?php

namespace App\Filament\Admin\Resources\Quotas\Schemas;

use Filament\Forms\Components\TextInput;
use Filament\Schemas\Schema;

class QuotaForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                TextInput::make('description')
                    ->label('Descrição')
                    ->required()
                    ->maxLength(255),
            ]);
    }
}

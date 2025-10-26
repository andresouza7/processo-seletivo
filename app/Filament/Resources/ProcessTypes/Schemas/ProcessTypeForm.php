<?php

namespace App\Filament\Resources\ProcessTypes\Schemas;

use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Schemas\Schema;

class ProcessTypeForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                TextInput::make('description')->label('Descrição'),
                TextInput::make('slug'),
                Select::make('quota_id')
                    ->label('Tipos da vaga')
                    ->multiple()
                    ->preload()
                    ->relationship('quotas', 'description')
            ]);
    }
}

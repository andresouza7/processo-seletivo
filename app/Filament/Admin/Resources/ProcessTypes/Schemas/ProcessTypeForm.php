<?php

namespace App\Filament\Admin\Resources\ProcessTypes\Schemas;

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
                TextInput::make('slug')
            ]);
    }
}

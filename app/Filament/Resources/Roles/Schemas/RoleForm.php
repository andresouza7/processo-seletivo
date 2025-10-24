<?php

namespace App\Filament\Resources\Roles\Schemas;

use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;

class RoleForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Section::make([
                    TextInput::make('name'),
                    Select::make('permission_id')
                        ->relationship('permissions', 'name')
                        ->multiple()
                        ->preload()
                ])
                ->heading('Configurar Perfil')
            ]);
    }
}

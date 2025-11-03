<?php

namespace App\Filament\Admin\Resources\Roles\Schemas;

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
                    TextInput::make('name')
                        ->label('Nome')
                        ->required(),
                    Select::make('permission_id')
                        ->label('Permissões')
                        ->required()
                        ->relationship('permissions', 'name')
                        ->multiple()
                        ->preload()
                ])
                    ->heading('Configurar Perfil')
            ]);
    }
}

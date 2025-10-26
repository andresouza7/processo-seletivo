<?php

namespace App\Filament\Admin\Resources\Roles\RelationManagers;

use App\Filament\Admin\Resources\Users\UserResource;
use Filament\Actions\CreateAction;
use Filament\Resources\RelationManagers\RelationManager;
use Filament\Tables\Table;

class UsersRelationManager extends RelationManager
{
    protected static string $relationship = 'users';

    protected static ?string $relatedResource = UserResource::class;

    public function table(Table $table): Table
    {
        return $table
            ->heading('Usuários Ativos')
            ->headerActions([
                CreateAction::make(),
            ]);
    }
}

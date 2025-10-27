<?php

namespace App\Filament\Admin\Resources\Roles\RelationManagers;

use App\Filament\Admin\Resources\Users\UserResource;
use App\Services\SelectionProcess\RoleService;
use Filament\Actions\CreateAction;
use Filament\Resources\RelationManagers\RelationManager;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;

class UsersRelationManager extends RelationManager
{
    protected static string $relationship = 'users';

    protected static ?string $relatedResource = UserResource::class;

    public function table(Table $table): Table
    {
        return $table
            ->heading('Usuários Ativos')
            ->columns([
                TextColumn::make('name')
                    ->label('Nome'),
                TextColumn::make('email'),
                TextColumn::make('created_at')
                    ->label('Acesso válido até')
                    ->badge()
                    ->color('success')
                    ->formatStateUsing(fn($record, RoleService $service) =>
                    $service->getUserRoleInfo($record)?->expires_at->format('d/m/Y')),
            ])
            ->headerActions([
                CreateAction::make(),
            ]);
    }
}

<?php

namespace App\Filament\Admin\Resources\Users\RelationManagers;

use App\Filament\Admin\Resources\Users\UserResource;
use App\Services\SelectionProcess\PermissionService;
use Filament\Actions\Action;
use Filament\Actions\AssociateAction;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\CreateAction;
use Filament\Actions\DeleteAction;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\DissociateAction;
use Filament\Actions\DissociateBulkAction;
use Filament\Actions\EditAction;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Notifications\Notification;
use Filament\Resources\RelationManagers\RelationManager;
use Filament\Schemas\Schema;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use Illuminate\Support\Facades\Auth;
use Spatie\Permission\Models\Role;

class UserRolesRelationManager extends RelationManager
{
    protected static string $relationship = 'userRoles';

    public function form(Schema $schema): Schema
    {
        return $schema
            ->components([
                Select::make('role_id')
                    ->label('Perfil')
                    ->options(fn() => Role::all()->pluck('name', 'id'))
                    ->required()
                    ->preload()
                    ->searchable(),
                TextInput::make('create_doc')
                    ->label('Documento de Origem')
                    ->required(),
            ]);
    }

    public function table(Table $table): Table
    {
        return $table
            ->heading('Histórico de Permissões')
            ->recordTitleAttribute('name')
            ->defaultSort('id', 'desc')
            ->columns([
                TextColumn::make('role.name')
                    ->label('Perfil')
                    ->searchable(),
                TextColumn::make('created_at')
                    ->label('Data de Atribuição')
                    ->date('d/m/Y h:i'),
                TextColumn::make('create_doc')
                    ->label('Documento de Origem')
                    ->searchable(),
                TextColumn::make('revoked_at')
                    ->label('Data de Revogação')
                    ->date('d/m/Y h:i'),
                TextColumn::make('revoke_doc')
                    ->label('Motivo da Revogação')
                    ->searchable(),
            ])
            ->filters([
                //
            ])
            ->recordActions([
                Action::make('revoke')
                    ->label('Remover')
                    ->modalSubmitActionLabel('Confirmar')
                    ->visible(fn($record) => is_null($record->revoked_at))
                    ->color('danger')
                    ->schema([
                        TextInput::make('revoke_doc')
                            ->label('Motivo da revogação')
                    ])
                    ->action(
                        fn($record, array $data, PermissionService $service) =>
                        $service->revokeUserRole($record, $data['revoke_doc'])
                    )
            ]);
    }
}

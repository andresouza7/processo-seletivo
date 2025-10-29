<?php

namespace App\Filament\Admin\Resources\Users;

use Filament\Schemas\Schema;
use Filament\Forms\Components\Select;
use Filament\Actions\EditAction;
use App\Filament\Admin\Resources\Users\Pages\ListUsers;
use App\Filament\Admin\Resources\Users\Pages\CreateUser;
use App\Filament\Admin\Resources\Users\Pages\EditUser;
use App\Filament\Admin\Resources\UserResource\Pages;
use App\Filament\Admin\Resources\UserResource\RelationManagers;
use App\Filament\Admin\Resources\Users\RelationManagers\UserRolesRelationManager;
use App\Models\User;
use App\Models\UserRole;
use App\Services\SelectionProcess\RoleService;
use Filament\Actions\Action;
use Filament\Forms;
use Filament\Forms\Components\TextInput;
use Filament\Notifications\Notification;
use Filament\Resources\Resource;
use Filament\Schemas\Components\Actions;
use Filament\Schemas\Components\Group;
use Filament\Schemas\Components\Section;
use Filament\Support\Icons\Heroicon;
use Filament\Tables;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;

class UserResource extends Resource
{
    protected static ?string $model = User::class;
    protected static ?string $modelLabel = 'Usuário';
    protected static string | \BackedEnum | null $navigationIcon = 'heroicon-o-users';
    protected static string | \UnitEnum | null $navigationGroup = 'Administrador';
    protected static ?int $navigationSort = 1;

    public static function form(Schema $schema): Schema
    {
        return $schema
            ->components([
                Section::make()
                    ->heading('Configurar conta')
                    ->schema([
                        TextInput::make('name')
                            ->label('Nome'),
                        TextInput::make('email'),
                        TextInput::make('role')
                            ->formatStateUsing(
                                fn($record, RoleService $service) =>
                                $service->getUserRole($record)?->name ?? 'nenhum'
                            )
                            ->label('Perfil')
                            ->readOnly()
                            ->helperText('Configure o perfil de acesso do usuário')
                            ->suffixAction(
                                fn() => Action::make('atribuirPerfil')
                                    ->label('Atribuir Perfil')
                                    ->modalSubmitActionLabel('Atribuir')
                                    ->icon(Heroicon::PencilSquare)
                                    ->schema([
                                        Group::make([
                                            Select::make('role_id')
                                                ->label('Perfil')
                                                ->options(\Spatie\Permission\Models\Role::pluck('name', 'id'))
                                                ->required()
                                                ->preload()
                                                ->searchable()
                                                ->columnSpan(3),
                                            TextInput::make('duration')
                                                ->label('Validade (dias)')
                                                ->integer()
                                                ->default(180)
                                                ->required()
                                                ->columnSpan(1),
                                        ])->columns(4),
                                        TextInput::make('create_doc')
                                            ->label('Documento')
                                            ->required(),
                                    ])
                                    ->action(function (array $data, $record, RoleService $service) {
                                        $service->assignUserRole(
                                            $record,
                                            $data['role_id'],
                                            $data['create_doc'],
                                            $data['duration']
                                        );
                                    })
                                    ->successNotification(
                                        Notification::make()
                                            ->title('Tudo certo')
                                            ->body('Perfil atribuído com sucesso')
                                    )
                                    ->successRedirectUrl(fn($record) => static::getUrl('edit', ['record' => $record]))
                            )

                    ])->columns(2)
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('name')->label('Nome'),
                TextColumn::make('email'),
            ])
            ->filters([
                //
            ])
            ->recordActions([
                EditAction::make(),
            ]);
    }

    public static function getRelations(): array
    {
        return [
            UserRolesRelationManager::class,
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => ListUsers::route('/'),
            'create' => CreateUser::route('/create'),
            'edit' => EditUser::route('/{record}/edit'),
        ];
    }
}

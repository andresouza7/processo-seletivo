<?php

namespace App\Filament\Resources;

use Filament\Schemas\Schema;
use Filament\Forms\Components\TextInput;
use Filament\Tables\Columns\TextColumn;
use Filament\Actions\EditAction;
use Filament\Actions\BulkActionGroup;
use App\Filament\Resources\TipoVagaResource\Pages\ListTipoVagas;
use App\Filament\Resources\TipoVagaResource\Pages\CreateTipoVaga;
use App\Filament\Resources\TipoVagaResource\Pages\EditTipoVaga;
use App\Filament\Resources\TipoVagaResource\Pages;
use App\Models\TipoVaga;
use Filament\Forms;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;

class TipoVagaResource extends Resource
{
    protected static ?string $model = TipoVaga::class;
    protected static ?string $modelLabel = 'Tipo de Vaga';
    protected static ?string $pluralModelLabel = 'Tipos de Vaga';
    protected static string | \BackedEnum | null $navigationIcon = 'heroicon-o-tag';
    protected static string | \UnitEnum | null $navigationGroup = 'Gerenciar';
    protected static ?int $navigationSort = 5;

    public static function form(Schema $schema): Schema
    {
        return $schema
            ->components([
                TextInput::make('descricao')
                    ->required()
                    ->maxLength(255),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->heading('Gerenciar tipo de vaga')
            ->description('Condição na qual um candidato poderá concorrer a uma vaga.')
            ->columns([
                TextColumn::make('id_tipo_vaga')->label('ID'),
                TextColumn::make('descricao')
                    ->searchable(),
            ])
            ->filters([
                //
            ])
            ->recordActions([
                EditAction::make(),
            ])
            ->toolbarActions([
                BulkActionGroup::make([
                    // Tables\Actions\DeleteBulkAction::make(),
                ]),
            ]);
    }

    public static function getRelations(): array
    {
        return [
            //
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => ListTipoVagas::route('/'),
            'create' => CreateTipoVaga::route('/create'),
            'edit' => EditTipoVaga::route('/{record}/edit'),
        ];
    }
}

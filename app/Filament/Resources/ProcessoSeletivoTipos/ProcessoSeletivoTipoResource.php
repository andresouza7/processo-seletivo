<?php

namespace App\Filament\Resources\ProcessoSeletivoTipos;

use Filament\Schemas\Schema;
use Filament\Tables\Columns\TextColumn;
use Filament\Actions\EditAction;
use Filament\Actions\BulkActionGroup;
use App\Filament\Resources\ProcessoSeletivoTipos\Pages\ListProcessoSeletivoTipos;
use App\Filament\Resources\ProcessoSeletivoTipos\Pages\CreateProcessoSeletivoTipo;
use App\Filament\Resources\ProcessoSeletivoTipos\Pages\EditProcessoSeletivoTipo;
use App\Filament\Resources\ProcessoSeletivoTipoResource\Pages;
use App\Models\ProcessoSeletivoTipo;
use Filament\Forms;
use Filament\Forms\Components\TextInput;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;

class ProcessoSeletivoTipoResource extends Resource
{
    protected static ?string $model = ProcessoSeletivoTipo::class;
    protected static ?string $modelLabel = 'Modalidade PS';
    protected static ?string $pluralModelLabel = 'Modalidades PS';
    protected static ?string $slug = 'modalidades';
    protected static string | \BackedEnum | null $navigationIcon = 'heroicon-o-tag';
    protected static string | \UnitEnum | null $navigationGroup = 'Gerenciar';
    protected static ?int $navigationSort = 4;

    public static function form(Schema $schema): Schema
    {
        return $schema
            ->components([
                //
                TextInput::make('descricao'),
                TextInput::make('chave'),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->heading('Gerenciar Tipos de Processos')
            ->description('Categoria na qual determinado processo se enquadra.')
            ->columns([
                //
                TextColumn::make('idprocesso_seletivo_tipo')->label('ID'),
                TextColumn::make('descricao')
                    ->searchable(),
                TextColumn::make('chave')
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
            'index' => ListProcessoSeletivoTipos::route('/'),
            'create' => CreateProcessoSeletivoTipo::route('/create'),
            'edit' => EditProcessoSeletivoTipo::route('/{record}/edit'),
        ];
    }
}

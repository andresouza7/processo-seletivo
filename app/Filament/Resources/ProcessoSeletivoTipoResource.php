<?php

namespace App\Filament\Resources;

use App\Filament\Resources\ProcessoSeletivoTipoResource\Pages;
use App\Models\ProcessoSeletivoTipo;
use Filament\Forms;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Form;
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
    protected static ?string $navigationIcon = 'heroicon-o-tag';
    protected static ?string $navigationGroup = 'Gerenciar';
    protected static ?int $navigationSort = 4;

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
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
                Tables\Columns\TextColumn::make('idprocesso_seletivo_tipo')->label('ID'),
                Tables\Columns\TextColumn::make('descricao')
                    ->searchable(),
                Tables\Columns\TextColumn::make('chave')
                    ->searchable(),
            ])
            ->filters([
                //
            ])
            ->actions([
                Tables\Actions\EditAction::make(),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
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
            'index' => Pages\ListProcessoSeletivoTipos::route('/'),
            'create' => Pages\CreateProcessoSeletivoTipo::route('/create'),
            'edit' => Pages\EditProcessoSeletivoTipo::route('/{record}/edit'),
        ];
    }
}

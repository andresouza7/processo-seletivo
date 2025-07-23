<?php

namespace App\Filament\Gps\Resources;

use App\Filament\Resources\TipoVagaResource\Pages;
use App\Models\TipoVaga;
use Filament\Forms;
use Filament\Forms\Form;
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
    protected static ?string $navigationIcon = 'heroicon-o-tag';
    protected static ?string $navigationGroup = 'Gerenciar';
    protected static ?int $navigationSort = 5;

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\TextInput::make('descricao')
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
                Tables\Columns\TextColumn::make('id_tipo_vaga')->label('ID'),
                Tables\Columns\TextColumn::make('descricao')
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
            'index' => Pages\ListTipoVagas::route('/'),
            'create' => Pages\CreateTipoVaga::route('/create'),
            'edit' => Pages\EditTipoVaga::route('/{record}/edit'),
        ];
    }
}

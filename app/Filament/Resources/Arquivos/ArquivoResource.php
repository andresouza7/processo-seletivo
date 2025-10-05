<?php

namespace App\Filament\Resources\Arquivos;

use Filament\Schemas\Schema;
use Filament\Forms\Components\TextInput;
use Filament\Tables\Columns\TextColumn;
use Filament\Actions\Action;
use Filament\Actions\BulkActionGroup;
use App\Filament\Resources\Arquivos\Pages\ListArquivos;
use App\Filament\Resources\Arquivos\Pages\CreateArquivo;
use App\Filament\Resources\Arquivos\Pages\EditArquivo;
use App\Filament\Resources\ArquivoResource\Pages;
use App\Filament\Resources\ArquivoResource\RelationManagers;
use App\Models\Arquivo;
use Filament\Forms;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;

class ArquivoResource extends Resource
{
    protected static ?string $model = Arquivo::class;
    protected static ?string $modelLabel = 'Arquivos Legados';
    protected static string | \BackedEnum | null $navigationIcon = 'heroicon-o-circle-stack';
    protected static string | \UnitEnum | null $navigationGroup = 'Administrador';
    protected static ?int $navigationSort = 3;

    public static function form(Schema $schema): Schema
    {
        return $schema
            ->components([
                TextInput::make('size')
                    ->required()
                    ->numeric(),
                TextInput::make('width')
                    ->required()
                    ->numeric(),
                TextInput::make('height')
                    ->required()
                    ->numeric(),
                TextInput::make('name')
                    ->required()
                    ->maxLength(255),
                TextInput::make('mimetype')
                    ->required()
                    ->maxLength(255),
                TextInput::make('descricao')
                    ->required()
                    ->maxLength(255),
                TextInput::make('codname')
                    ->required()
                    ->maxLength(255),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->heading('Detalhes de arquivos')
            ->description('Para consulta de metadados dos arquivos armazenados no sistema.')
            ->columns([
                TextColumn::make('size')
                    ->numeric()
                    ->sortable(),
                TextColumn::make('width')
                    ->numeric()
                    ->sortable(),
                TextColumn::make('height')
                    ->numeric()
                    ->sortable(),
                TextColumn::make('name')
                    ->searchable(),
                TextColumn::make('mimetype')
                    ->searchable(),
                TextColumn::make('descricao')
                    ->searchable(),
                TextColumn::make('codname')
                    ->searchable(),
            ])
            ->filters([
                //
            ])
            ->recordActions([
                // Tables\Actions\EditAction::make(),
                Action::make('download')
                    ->label('Download')
                    ->icon('heroicon-o-arrow-down-tray')
                    ->url(fn($record) => optional($record->processo_seletivo_anexo)->url_arquivo)
                    ->openUrlInNewTab(),
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
            'index' => ListArquivos::route('/'),
            'create' => CreateArquivo::route('/create'),
            'edit' => EditArquivo::route('/{record}/edit'),
        ];
    }
}

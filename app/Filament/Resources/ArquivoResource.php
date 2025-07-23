<?php

namespace App\Filament\Resources;

use App\Filament\Resources\ArquivoResource\Pages;
use App\Filament\Resources\ArquivoResource\RelationManagers;
use App\Models\Arquivo;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;

class ArquivoResource extends Resource
{
    protected static ?string $model = Arquivo::class;
    protected static ?string $navigationIcon = 'heroicon-o-circle-stack';
    protected static ?string $navigationGroup = 'Administrador';
    protected static ?int $navigationSort = 3;

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\TextInput::make('size')
                    ->required()
                    ->numeric(),
                Forms\Components\TextInput::make('width')
                    ->required()
                    ->numeric(),
                Forms\Components\TextInput::make('height')
                    ->required()
                    ->numeric(),
                Forms\Components\TextInput::make('name')
                    ->required()
                    ->maxLength(255),
                Forms\Components\TextInput::make('mimetype')
                    ->required()
                    ->maxLength(255),
                Forms\Components\TextInput::make('descricao')
                    ->required()
                    ->maxLength(255),
                Forms\Components\TextInput::make('codname')
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
                Tables\Columns\TextColumn::make('size')
                    ->numeric()
                    ->sortable(),
                Tables\Columns\TextColumn::make('width')
                    ->numeric()
                    ->sortable(),
                Tables\Columns\TextColumn::make('height')
                    ->numeric()
                    ->sortable(),
                Tables\Columns\TextColumn::make('name')
                    ->searchable(),
                Tables\Columns\TextColumn::make('mimetype')
                    ->searchable(),
                Tables\Columns\TextColumn::make('descricao')
                    ->searchable(),
                Tables\Columns\TextColumn::make('codname')
                    ->searchable(),
            ])
            ->filters([
                //
            ])
            ->actions([
                // Tables\Actions\EditAction::make(),
                Tables\Actions\Action::make('download')
                    ->label('Download')
                    ->icon('heroicon-o-arrow-down-tray')
                    ->url(fn($record) => optional($record->processo_seletivo_anexo)->url_arquivo)
                    ->openUrlInNewTab(),
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
            'index' => Pages\ListArquivos::route('/'),
            'create' => Pages\CreateArquivo::route('/create'),
            'edit' => Pages\EditArquivo::route('/{record}/edit'),
        ];
    }
}

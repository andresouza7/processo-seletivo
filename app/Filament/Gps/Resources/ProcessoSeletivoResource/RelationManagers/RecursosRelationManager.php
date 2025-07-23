<?php

namespace App\Filament\Gps\Resources\ProcessoSeletivoResource\RelationManagers;

use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\RelationManagers\RelationManager;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;

class RecursosRelationManager extends RelationManager
{
    protected static string $relationship = 'recursos';

    public function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Textarea::make('descricao')
                    ->columnSpanFull()
                    ->disabled(),
                Forms\Components\Select::make('situacao')
                    ->required()
                    ->options([
                        'D' => 'Deferido',
                        'I' => 'Indeferido'
                    ]),
                Forms\Components\Textarea::make('resposta')
                    ->columnSpanFull()
                    ->required()
                    ->maxLength(255),
            ]);
    }

    public function table(Table $table): Table
    {
        return $table
            ->recordTitleAttribute('idrecurso')
            ->defaultSort('idrecurso', 'desc')
            ->columns([
                Tables\Columns\TextColumn::make('idrecurso'),
                Tables\Columns\TextColumn::make('inscricao.inscricao_vaga.codigo')
                    ->label('Cód Vaga'),
                Tables\Columns\TextColumn::make('inscricao.inscricao_vaga.descricao')
                    ->label('Descrição Vaga'),
                Tables\Columns\TextColumn::make('situacao')
                    ->badge()
            ])
            ->filters([
                Tables\Filters\Filter::make('situacao_null')
                    ->label('Pendentes')
                    ->query(fn(Builder $query): Builder => $query->whereNull('situacao'))
                    ->default(true),
            ])
            ->headerActions([
                // Tables\Actions\CreateAction::make(),
            ])
            ->actions([
                Tables\Actions\EditAction::make()->label('Responder'),
                // Tables\Actions\DeleteAction::make(),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    // Tables\Actions\DeleteBulkAction::make(),
                ]),
            ]);
    }
}

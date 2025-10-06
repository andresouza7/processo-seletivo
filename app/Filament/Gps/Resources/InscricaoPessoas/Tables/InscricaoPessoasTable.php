<?php

namespace App\Filament\Gps\Resources\InscricaoPessoas\Tables;

use Filament\Actions\ViewAction;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;

class InscricaoPessoasTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->heading('Contas de candidatos')
            ->description('Gerência dos dados cadastrais dos dandidatos e redefinição de senha.')
            ->defaultSort('nome')
            ->columns([
                TextColumn::make('nome')
                    ->searchable(),
                TextColumn::make('sexo')
                    ->badge()
                    ->searchable(),
                TextColumn::make('ci')
                    ->label('RG')
                    ->searchable(),
                TextColumn::make('cpf')
                    ->label('CPF')
                    ->searchable(),
                TextColumn::make('email')
                    ->searchable(),
            ])
            ->filters([
                //
            ])
            ->recordActions([
                ViewAction::make(),
            ]);
    }
}

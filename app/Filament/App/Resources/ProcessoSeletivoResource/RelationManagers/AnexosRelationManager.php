<?php

namespace App\Filament\App\Resources\ProcessoSeletivoResource\RelationManagers;

use Carbon\Carbon;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\RelationManagers\RelationManager;
use Filament\Tables;
use Filament\Tables\Columns\Layout\Stack;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;

class AnexosRelationManager extends RelationManager
{
    protected static string $relationship = 'anexos';

    public function form(Form $form): Form
    {
        return $form
            ->schema([]);
    }

    public function table(Table $table): Table
    {
        return $table
            ->recordTitleAttribute('descricao')
            ->heading('Publicações')
            ->defaultSort('idprocesso_seletivo_anexo', 'desc')
            ->columns([
                Stack::make([
                    Tables\Columns\TextColumn::make('data_publicacao')
                        ->formatStateUsing(fn($record) => sprintf(
                            "<div class='text-xs font-semibold flex gap-3'><span>%s</span></div>",
                            Carbon::parse($record->data_publicacao)->format('d/m/Y'),
                        ))->html()
                        ->color('gray'),
                    Tables\Columns\TextColumn::make('descricao')
                        ->label('Descrição')
                        ->color('primary')
                        ->url(fn($record) => $record->url_arquivo, shouldOpenInNewTab: true)
                        ->searchable(),
                ])
            ])
            ->filters([
                //
            ])
            ->headerActions([
                Tables\Actions\CreateAction::make(),
            ])
            ->actions([
                Tables\Actions\EditAction::make(),
                Tables\Actions\DeleteAction::make(),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make(),
                ]),
            ]);
    }
}

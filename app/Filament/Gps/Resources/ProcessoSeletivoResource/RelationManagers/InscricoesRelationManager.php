<?php

namespace App\Filament\Gps\Resources\ProcessoSeletivoResource\RelationManagers;

use App\Filament\Exports\InscricaoExporter;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\RelationManagers\RelationManager;
use Filament\Tables;
use Filament\Tables\Actions\ExportAction;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;

class InscricoesRelationManager extends RelationManager
{
    protected static string $relationship = 'inscricoes';

    protected static ?string $title = 'Inscritos';

    public function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\TextInput::make('cod_inscricao')
                    ->required()
                    ->maxLength(255),
            ]);
    }

    public function table(Table $table): Table
    {
        return $table
            ->recordTitleAttribute('cod_inscricao')
            ->columns([
                Tables\Columns\TextColumn::make('cod_inscricao'),
                Tables\Columns\TextColumn::make('inscricao_pessoa.nome'),
                Tables\Columns\TextColumn::make('inscricao_vaga.descricao'),
                Tables\Columns\TextColumn::make('tipo_vaga.descricao'),
            ])
            ->filters([
                //
            ])
            ->headerActions([
                // Tables\Actions\CreateAction::make(),
                ExportAction::make()
                    ->label('Exportar para planilha')
                    ->exporter(InscricaoExporter::class)
            ])
            ->actions([
                Tables\Actions\EditAction::make(),
                Tables\Actions\DeleteAction::make(),
                Tables\Actions\Action::make('ver_anexo')
                    ->label('Ver Anexo')
                    ->icon('heroicon-o-eye')
                    // ->url(fn($record) => route('inscricoes.anexo', $record->idinscricao)) // or specify collection if needed
                    ->openUrlInNewTab()
                    ->visible(fn($record) => $record->hasMedia('inscricao_anexos')),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    // Tables\Actions\DeleteBulkAction::make(),
                ]),
            ]);
    }
}

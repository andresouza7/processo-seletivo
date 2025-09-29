<?php

namespace App\Filament\Gps\Resources\ProcessoSeletivoResource\Pages;

use App\Filament\Exports\InscricaoExporter;
use App\Filament\Gps\Resources\ProcessoSeletivoResource;
use Filament\Actions;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Infolists\Components\Section;
use Filament\Infolists\Components\TextEntry;
use Filament\Infolists\Infolist;
use Filament\Resources\Pages\ManageRelatedRecords;
use Filament\Tables;
use Filament\Tables\Actions\ExportAction;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;

class ManageAvaliadores extends ManageRelatedRecords
{
    protected static string $resource = ProcessoSeletivoResource::class;
    protected static ?string $title = 'Gerenciar Avaliadores';
    protected static string $relationship = 'avaliadores';
    protected static ?string $navigationIcon = 'heroicon-o-users';

    public static function getNavigationLabel(): string
    {
        return 'Avaliadores';
    }

    public function infolist(Infolist $infolist): Infolist
    {
        return $infolist
            ->schema([
                Section::make('Candidato')
                    ->schema([
                        TextEntry::make('inscricao_pessoa.nome')->label('Nome'),
                        TextEntry::make('inscricao_pessoa.ci')->label('Documento Identidade'),
                        TextEntry::make('inscricao_pessoa.cpf')->label('CPF'),
                        TextEntry::make('inscricao_pessoa.endereco')->label('Endereço'),
                        TextEntry::make('inscricao_pessoa.bairro')->label('Bairro'),
                        TextEntry::make('inscricao_pessoa.cidade')->label('Cidade'),
                        TextEntry::make('inscricao_pessoa.email')->label('Email'),
                    ])
                    ->columns(2),
            ]);
    }

    public function form(Form $form): Form
    {
        return $form
            ->schema([]);
    }

    public function table(Table $table): Table
    {
        return $table
            ->recordTitleAttribute('name')
            ->inverseRelationship('processos_seletivos')
            ->heading('Avaliadores')
            ->columns([
                Tables\Columns\TextColumn::make('id')
                    ->label('ID')
                    ->sortable(),
                Tables\Columns\TextColumn::make('name')
                    ->label('Nome')
                    ->searchable(),
            ])
            ->filters([
                //
            ])
            ->headerActions([
                Tables\Actions\AttachAction::make()->label('Cadastrar Avaliador')->preloadRecordSelect()
            ])
            ->actions([
                Tables\Actions\ViewAction::make(),
                // Tables\Actions\EditAction::make(),
                Tables\Actions\DetachAction::make()->label('Remover Avaliador')->requiresConfirmation(),
            ])
            ->bulkActions([
                // Tables\Actions\BulkActionGroup::make([
                //     Tables\Actions\DissociateBulkAction::make(),
                //     Tables\Actions\DeleteBulkAction::make(),
                // ]),
            ]);
    }
}

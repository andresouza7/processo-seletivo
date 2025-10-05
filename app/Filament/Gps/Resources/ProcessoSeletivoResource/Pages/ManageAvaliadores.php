<?php

namespace App\Filament\Gps\Resources\ProcessoSeletivoResource\Pages;

use Filament\Schemas\Schema;
use Filament\Schemas\Components\Section;
use Filament\Tables\Columns\TextColumn;
use Filament\Actions\AttachAction;
use Filament\Actions\ViewAction;
use Filament\Actions\DetachAction;
use App\Filament\Exports\InscricaoExporter;
use App\Filament\Gps\Resources\ProcessoSeletivoResource;
use Filament\Actions;
use Filament\Forms;
use Filament\Infolists\Components\TextEntry;
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
    protected static string | \BackedEnum | null $navigationIcon = 'heroicon-o-users';

    public static function getNavigationLabel(): string
    {
        return 'Avaliadores';
    }

    public function infolist(Schema $schema): Schema
    {
        return $schema
            ->components([
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

    public function form(Schema $schema): Schema
    {
        return $schema
            ->components([]);
    }

    public function table(Table $table): Table
    {
        return $table
            ->recordTitleAttribute('name')
            ->inverseRelationship('processos_seletivos')
            ->heading('Avaliadores')
            ->columns([
                TextColumn::make('id')
                    ->label('ID')
                    ->sortable(),
                TextColumn::make('name')
                    ->label('Nome')
                    ->searchable(),
            ])
            ->filters([
                //
            ])
            ->headerActions([
                AttachAction::make()->label('Cadastrar Avaliador')->preloadRecordSelect()
            ])
            ->recordActions([
                ViewAction::make(),
                // Tables\Actions\EditAction::make(),
                DetachAction::make()->label('Remover Avaliador')->requiresConfirmation(),
            ])
            ->toolbarActions([
                // Tables\Actions\BulkActionGroup::make([
                //     Tables\Actions\DissociateBulkAction::make(),
                //     Tables\Actions\DeleteBulkAction::make(),
                // ]),
            ]);
    }
}

<?php

namespace App\Filament\Gps\Resources\ProcessoSeletivos\Pages;

use Filament\Schemas\Schema;
use Filament\Schemas\Components\Section;
use Filament\Tables\Columns\TextColumn;
use Filament\Actions\ExportAction;
use Filament\Actions\ViewAction;
use Filament\Actions\DeleteAction;
use App\Filament\Exports\InscricaoExporter;
use App\Filament\Gps\Resources\ProcessoSeletivos\ProcessoSeletivoResource;
use Filament\Actions;
use Filament\Forms;
use Filament\Infolists\Components\TextEntry;
use Filament\Resources\Pages\ManageRelatedRecords;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;

class ManageInscritos extends ManageRelatedRecords
{
    protected static string $resource = ProcessoSeletivoResource::class;
    protected static ?string $title = 'Gerenciar Inscrições';
    protected static string $relationship = 'inscricoes';
    protected static ?string $navigationLabel = 'Inscrições';
    protected static string | \BackedEnum | null $navigationIcon = 'heroicon-o-users';

    public function infolist(Schema $schema): Schema
    {
        return $schema
            ->components([
                // Candidato Section
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

                // Inscrição Section
                Section::make('Inscrição')
                    ->schema([
                        TextEntry::make('code')->label('Cód. Inscrição'),
                        TextEntry::make('inscricao_vaga.descricao')->label('Vaga'),
                        TextEntry::make('tipo_vaga.descricao')->label('Tipo de Vaga'),
                    ])
                    ->columns(2),

                TextEntry::make('code')
                    ->label('Documentos')
                    ->formatStateUsing(function ($record) {
                        // $record é a instância do modelo
                        $links = $record->getMedia()->map(function ($media) {
                            $route = route('media.temp', $media?->uuid);
                            return '<a href="' . $route . '" target="_blank" class="text-blue-600 hover:underline">' . $media->file_name . '</a>';
                        })->implode('<br>');

                        return $links ?: '-';
                    })
                    ->color('primary')
                    ->html(), 

                    TextEntry::make('code')
                    ->label('Laudo Médico')
                    ->formatStateUsing(function ($record) {
                        $link = tempMediaUrl($record, 'laudo_medico');
                        $text = '<a href="' . $link . '" target="_blank" class="text-blue-600 hover:underline">Abrir</a>';

                        return $link ? $text : '-';
                    })
                    ->color('primary')
                    ->html(), 

                    TextEntry::make('code')
                    ->label('Isenção Taxa')
                    ->formatStateUsing(function ($record) {
                        $link = tempMediaUrl($record, 'isencao_taxa');
                        $text = '<a href="' . $link . '" target="_blank" class="text-blue-600 hover:underline">Abrir</a>';

                        return $link ? $text : '-';
                    })
                    ->color('primary')
                    ->html(),
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
            ->recordTitleAttribute('code')
            ->defaultSort('idinscricao', 'desc')
            ->heading('Inscrições')
            ->columns([
                TextColumn::make('code')
                    ->label('Cód. Inscrição')
                    ->searchable(),
                TextColumn::make('inscricao_pessoa.nome')
                    ->label('Candidato')
                    ->searchable(),
            ])
            ->filters([
                //
            ])
            ->headerActions([
                // Tables\Actions\CreateAction::make(),
                ExportAction::make()
                    ->label('Exportar para planilha')
                    ->color('primary')
                    ->exporter(InscricaoExporter::class)
            ])
            ->recordActions([
                ViewAction::make(),
                // Tables\Actions\EditAction::make(),
                DeleteAction::make(),
            ])
            ->toolbarActions([
                // Tables\Actions\BulkActionGroup::make([
                //     Tables\Actions\DissociateBulkAction::make(),
                //     Tables\Actions\DeleteBulkAction::make(),
                // ]),
            ]);
    }
}

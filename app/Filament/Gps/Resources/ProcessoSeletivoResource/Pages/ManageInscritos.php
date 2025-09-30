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

class ManageInscritos extends ManageRelatedRecords
{
    protected static string $resource = ProcessoSeletivoResource::class;
    protected static ?string $title = 'Gerenciar Inscrições';
    protected static string $relationship = 'inscricoes';
    protected static ?string $navigationIcon = 'heroicon-o-users';

    public static function getNavigationLabel(): string
    {
        return 'Inscricoes';
    }

    public function infolist(Infolist $infolist): Infolist
    {
        return $infolist
            ->schema([
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
                        TextEntry::make('cod_inscricao')->label('Cód. Inscrição'),
                        TextEntry::make('inscricao_vaga.descricao')->label('Vaga'),
                        TextEntry::make('tipo_vaga.descricao')->label('Tipo de Vaga'),
                    ])
                    ->columns(2),



                TextEntry::make('cod_inscricao')
                    ->label('Documentos')
                    ->formatStateUsing(function ($record) {
                        // $record é a instância do modelo
                        $links = $record->getMedia()->map(function ($media) use ($record) {
                            return '<a href="' . tempMediaUrl($record) . '" target="_blank" class="text-blue-600 hover:underline">' . $media->file_name . '</a>';
                        })->implode('<br>');

                        return $links ?: '-';
                    })
                    ->html(), // precisa habilitar HTML
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
            ->recordTitleAttribute('cod_inscricao')
            ->defaultSort('idinscricao', 'desc')
            ->heading('Inscrições')
            ->columns([
                Tables\Columns\TextColumn::make('cod_inscricao')
                    ->label('Cód. Inscrição')
                    ->searchable(),
                Tables\Columns\TextColumn::make('inscricao_pessoa.nome')
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
            ->actions([
                Tables\Actions\ViewAction::make(),
                // Tables\Actions\EditAction::make(),
                Tables\Actions\DeleteAction::make(),
                Tables\Actions\Action::make('ver_anexo')
                    ->label('Anexos')
                    ->icon('heroicon-o-eye')
                    ->url(fn($record) => tempMediaUrl($record))
                    ->openUrlInNewTab()
                    ->visible(fn($record) => $record->hasMedia()),

                Tables\Actions\Action::make('ver_laudo')
                    ->label('Laudo')
                    ->icon('heroicon-o-eye')
                    ->url(fn($record) => tempMediaUrl($record))
                    ->openUrlInNewTab()
                    ->visible(fn($record) => $record->hasMedia('laudo_medico')),
            ])
            ->bulkActions([
                // Tables\Actions\BulkActionGroup::make([
                //     Tables\Actions\DissociateBulkAction::make(),
                //     Tables\Actions\DeleteBulkAction::make(),
                // ]),
            ]);
    }
}

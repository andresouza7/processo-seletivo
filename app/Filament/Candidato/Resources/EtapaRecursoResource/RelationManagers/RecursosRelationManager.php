<?php

namespace App\Filament\Candidato\Resources\EtapaRecursoResource\RelationManagers;

use App\Models\Inscricao;
use App\Models\Recurso;
use Filament\Forms;
use Filament\Forms\Components\SpatieMediaLibraryFileUpload;
use Filament\Forms\Form;
use Filament\Forms\Get;
use Filament\Infolists\Components\Actions;
use Filament\Infolists\Components\Actions\Action;
use Filament\Infolists\Components\TextEntry;
use Filament\Infolists\Infolist;
use Filament\Notifications\Notification;
use Filament\Resources\RelationManagers\RelationManager;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use Illuminate\Support\Facades\Auth;

class RecursosRelationManager extends RelationManager
{
    protected static string $relationship = 'recursos';

    public $bloquear_recurso = false;

    public function mount(): void
    {
        $etapa_recurso = $this->getOwnerRecord();

        $periodo_recurso_encerrado = !$etapa_recurso->aceita_recurso;

        // verifica se o candidato ja criou um recurso para esta etapa
        $recurso_existente = Recurso::where('idetapa_recurso', $etapa_recurso->idetapa_recurso)
            ->where('idinscricao_pessoa', auth()->guard('candidato')->id())->first();

        $this->bloquear_recurso = ($recurso_existente && !$etapa_recurso->permite_multiplos_recursos) || $periodo_recurso_encerrado;
    }

    public function infolist(Infolist $infolist): Infolist
    {
        return $infolist
            ->schema([
                TextEntry::make('descricao'),
                TextEntry::make('resposta'),
                TextEntry::make('situacao'),
                Actions::make([
                    Action::make('anexo_avaliador')
                        ->label('Documento')
                        ->url(fn($record) => tempMediaUrl($record, 'anexo_avaliador'))
                        ->openUrlInNewTab()
                        ->visible(fn($record) => $record->hasMedia('anexo_avaliador')),
                ])
            ]);
    }

    public function form(Form $form): Form
    {
        return $form
            ->schema([
                // Forms\Components\Select::make('idinscricao')
                //     ->label('Inscrição')
                //     ->helperText('* Selecione a inscrição para a qual deseja entrar com recurso')
                //     ->columnSpanFull()
                //     ->required()
                //     ->options(function () {
                //         $ownerRecord = $this->getOwnerRecord();
                //         $idProcessoSeletivo = $ownerRecord->idprocesso_seletivo;

                //         return \App\Models\Inscricao::query()
                //             ->where('idprocesso_seletivo', $idProcessoSeletivo)
                //             ->where('idinscricao_pessoa', Auth::guard('candidato')->id())
                //             ->with(['inscricao_vaga', 'tipo_vaga']) // eager load the relation
                //             ->get()
                //             ->mapWithKeys(function ($inscricao) {
                //                 $label = "{$inscricao->idinscricao} => {$inscricao->inscricao_vaga->descricao} => {$inscricao->tipo_vaga->descricao}";
                //                 return [$inscricao->idinscricao => $label];
                //             });
                //     }),
                Forms\Components\Textarea::make('descricao')
                    ->label('Justificativa')
                    ->required()
                    ->columnSpanFull(),
                SpatieMediaLibraryFileUpload::make('anexo_candidato')
                    ->visible(fn() => $this->getOwnerRecord()->requer_anexos)
                    ->columnSpanFull()
                    ->maxFiles(1)
                    ->disk('local')
                    ->collection('anexo_candidato')
                    ->rules(['file', 'mimes:pdf', 'max:10240'])
            ]);
    }

    public function table(Table $table): Table
    {
        return $table
            ->recordTitleAttribute('idrecurso')
            ->heading('Recursos Enviados')
            ->description('Para interpor um recurso, utilize o botão "Abrir Recurso". Atente-se ao prazo previsto no edital.')
            ->paginated(false)
            ->columns([
                Tables\Columns\TextColumn::make('descricao')->limit()->label('Descrição'),
                Tables\Columns\TextColumn::make('data_hora')->dateTime('d/m/Y H:i'),
            ])
            ->filters([
                //
            ])
            ->headerActions([
                Tables\Actions\CreateAction::make()
                    ->label('Abrir Recurso')
                    ->modalSubmitActionLabel("Confirmar")
                    ->createAnother(false)
                    ->visible(fn() => !$this->bloquear_recurso)
                    ->mutateFormDataUsing(function (array $data, $record) {
                        // $inscricao = Inscricao::where('idinscricao', $data['idinscricao'])->first();

                        // $data['idprocesso_seletivo'] = $inscricao->idprocesso_seletivo;
                        // $data['idinscricao_pessoa'] = $inscricao->idinscricao_pessoa;
                        // $data['idinscricao'] = $inscricao->idinscricao;

                        $data['idprocesso_seletivo'] = $this->getOwnerRecord()->processo_seletivo->idprocesso_seletivo;
                        $data['idinscricao_pessoa'] = auth()->guard('candidato')->id();

                        return $data;
                    })
                    ->after(function ($livewire, $record) {
                        $livewire->redirect(route('filament.candidato.resources.etapa-recursos.edit', $this->getOwnerRecord()->idetapa_recurso));
                    })
                    ->successNotification(function ($record) {
                        Notification::make()
                            ->title('Recurso aberto com sucesso!')
                            ->body("Seu recurso foi registrado pelo sistema")
                            ->success()
                            ->duration(5000)
                            ->send();
                    }),
            ])
            ->actions([
                Tables\Actions\ViewAction::make()
                    ->visible(fn() => $this->getOwnerRecord()->resultado_disponivel)
                    ->label('Consultar Resultado'),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    // Tables\Actions\DeleteBulkAction::make(),
                ]),
            ]);
    }
}

<?php

namespace App\Filament\Candidato\Resources\EtapaRecursos\RelationManagers;

use Filament\Schemas\Schema;
use Filament\Schemas\Components\Actions;
use Filament\Actions\Action;
use Filament\Forms\Components\Textarea;
use Filament\Tables\Columns\TextColumn;
use Filament\Actions\CreateAction;
use Filament\Actions\ViewAction;
use Filament\Actions\BulkActionGroup;
use App\Models\Application;
use App\Models\Appeal;
use Filament\Forms;
use Filament\Forms\Components\SpatieMediaLibraryFileUpload;
use Filament\Forms\Get;
use Filament\Infolists\Components\TextEntry;
use Filament\Notifications\Notification;
use Filament\Resources\RelationManagers\RelationManager;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use Illuminate\Support\Facades\Auth;

class RecursosRelationManager extends RelationManager
{
    protected static string $relationship = 'appeals';

    public $bloquear_recurso = false;

    public function mount(): void
    {
        $appeal_stage = $this->getOwnerRecord();

        $periodo_recurso_encerrado = !$appeal_stage->can_appeal;

        // verifica se o candidato ja criou um recurso para esta etapa
        $recurso_existente = Appeal::where('idetapa_recurso', $appeal_stage->idetapa_recurso)
            ->where('idinscricao_pessoa', auth()->guard('candidato')->id())->first();

        $this->bloquear_recurso = ($recurso_existente && !$appeal_stage->allow_many) || $periodo_recurso_encerrado;
    }

    public function infolist(Schema $schema): Schema
    {
        return $schema
            ->components([
                TextEntry::make('description'),
                TextEntry::make('response'),
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

    public function form(Schema $schema): Schema
    {
        return $schema
            ->components([
                // Forms\Components\Select::make('idinscricao')
                //     ->label('Inscrição')
                //     ->helperText('* Selecione a inscrição para a qual deseja entrar com recurso')
                //     ->columnSpanFull()
                //     ->required()
                //     ->options(function () {
                //         $ownerRecord = $this->getOwnerRecord();
                //         $idProcessoSeletivo = $ownerRecord->id;

                //         return \App\Models\Application::query()
                //             ->where('id', $idProcessoSeletivo)
                //             ->where('idinscricao_pessoa', Auth::guard('candidato')->id())
                //             ->with(['position', 'quota']) // eager load the relation
                //             ->get()
                //             ->mapWithKeys(function ($application) {
                //                 $label = "{$application->idinscricao} => {$application->position->description} => {$application->quota->description}";
                //                 return [$application->idinscricao => $label];
                //             });
                //     }),
                Textarea::make('description')
                    ->label('Justificativa')
                    ->required()
                    ->columnSpanFull(),
                SpatieMediaLibraryFileUpload::make('anexo_candidato')
                    ->visible(fn() => $this->getOwnerRecord()->has_attachments)
                    ->columnSpanFull()
                    ->maxFiles(1)
                    ->disk('local')
                    ->collection('anexo_candidato')
                    ->rules(['file', 'mimes:pdf', 'max:2048'])
            ]);
    }

    public function table(Table $table): Table
    {
        return $table
            ->recordTitleAttribute('idrecurso')
            ->heading('Recursos Enviados')
            ->description('Para interpor um recurso, utilize o botão "Abrir Appeal". Atente-se ao prazo previsto no edital.')
            ->paginated(false)
            ->columns([
                TextColumn::make('description')->limit()->label('Descrição'),
                TextColumn::make('submitted_at')->dateTime('d/m/Y H:i'),
            ])
            ->filters([
                //
            ])
            ->headerActions([
                CreateAction::make()
                    ->label('Abrir Appeal')
                    ->modalSubmitActionLabel("Confirmar")
                    ->createAnother(false)
                    ->visible(fn() => !$this->bloquear_recurso)
                    ->mutateDataUsing(function (array $data, $record) {
                        // $application = Application::where('idinscricao', $data['idinscricao'])->first();

                        // $data['id'] = $application->id;
                        // $data['idinscricao_pessoa'] = $application->idinscricao_pessoa;
                        // $data['idinscricao'] = $application->idinscricao;

                        $data['id'] = $this->getOwnerRecord()->process->id;
                        $data['idinscricao_pessoa'] = auth()->guard('candidato')->id();

                        return $data;
                    })
                    ->after(function ($livewire, $record) {
                        $livewire->redirect(route('filament.candidato.resources.etapa-recursos.edit', $this->getOwnerRecord()->idetapa_recurso));
                    })
                    ->successNotification(function ($record) {
                        Notification::make()
                            ->title('Appeal aberto com sucesso!')
                            ->body("Seu recurso foi registrado pelo sistema")
                            ->success()
                            ->duration(5000)
                            ->send();
                    }),
            ])
            ->recordActions([
                ViewAction::make()
                    ->visible(fn() => $this->getOwnerRecord()->has_result)
                    ->label('Consultar Resultado'),
            ])
            ->toolbarActions([
                BulkActionGroup::make([
                    // Tables\Actions\DeleteBulkAction::make(),
                ]),
            ]);
    }
}

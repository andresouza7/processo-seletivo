<?php

namespace App\Filament\Candidato\Resources\Applications\Schemas;

use App\Filament\Components\AttachmentUpload;
use App\Filament\Components\DynamicFormFields;
use App\Models\Position;
use App\Models\Process;
use Filament\Forms\Components\Checkbox;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\SpatieMediaLibraryFileUpload;
use Filament\Forms\Components\Textarea;
use Filament\Schemas\Components\Fieldset;
use Filament\Schemas\Components\Grid;
use Filament\Schemas\Components\Group;
use Filament\Schemas\Components\Utilities\Get;
use Filament\Schemas\Schema;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Str;
use Livewire\Features\SupportFileUploads\TemporaryUploadedFile;

class ApplicationForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema->components([
            Grid::make(['default' => 1, 'lg' => 2])
                ->schema([
                    Grid::make()
                        ->columnSpan(['default' => 1, 'lg' => 1])
                        ->schema([
                            ...self::getProcessoSeletivoSection(),
                            ...self::getDocumentosSection(),
                            ...self::getVagaSection(),
                            ...self::getTipoVagaSection(),
                            ...self::getAtendimentoSection(),
                            ...self::getIsencaoSection(),
                            ...self::getTermosSection(),
                        ]),
                ]),
        ]);
    }

    // --------------------------
    // SECTION: Processo Seletivo
    // --------------------------
    private static function getProcessoSeletivoSection(): array
    {
        return [
            Select::make('process_id')
                ->label('Processo Seletivo')
                // ->options(fn() => Cache::remember('processos_inscricoes_abertas_options', 60, function () {
                //     return Process::inscricoesAbertas()
                //         ->get()
                //         ->mapWithKeys(fn($p) => [$p->id => "{$p->number} - {$p->title}"])
                //         ->toArray();
                // }))
                ->options(function () {
                    return Process::inscricoesAbertas()
                        ->get()
                        ->mapWithKeys(fn($p) => [$p->id => "{$p->number} - {$p->title}"])
                        ->toArray();
                })
                ->noSearchResultsMessage('Nenhum Processo Seletivo encontrado')
                ->placeholder('Selecione um processo seletivo')
                ->searchable()
                ->preload()
                ->required()
                ->columnSpanFull()
                ->live()
                ->afterStateUpdated(function (callable $set, $state) {
                    $processo = Process::find($state);

                    // dd($processo);
                    $set('has_fee', $processo?->has_fee);
                    $set('attachment_fields', (array) ($processo?->attachment_fields ?? []));
                }),
        ];
    }

    // --------------------------
    // SECTION: Documentos
    // --------------------------
    private static function getDocumentosSection(): array
    {
        return [
            DynamicFormFields::make('')
                ->label('Campos do formulário')
                ->columnSpanFull()
                ->hidden(fn(Get $get) => blank($get('process_id')))
                ->processId(fn(Get $get) => $get('process_id')),

            Group::make()
                ->hidden(fn(Get $get) => empty($get('attachment_fields')))
                ->schema(function (Get $get) {
                    $attachments = (array) $get('attachment_fields');

                    return [
                        Fieldset::make('Documentos Requeridos')
                            ->schema(
                                collect($attachments)->map(function ($item, $index) {
                                    $label = $item['item'] ?? "documento_{$index}";
                                    $description = $item['description'] ?? '';
                                    $fieldKey = 'documentos_requeridos_' . Str::slug("{$index}_{$label}");

                                    return AttachmentUpload::make($fieldKey)
                                        ->helperText($description)
                                        ->label($label)
                                        ->mediaName($label)
                                        ->disk('local');
                                })->toArray()
                            ),
                    ];
                })
                ->columnSpanFull(),
        ];
    }

    // --------------------------
    // SECTION: Vaga
    // --------------------------
    private static function getVagaSection(): array
    {
        return [
            Select::make('position_id')
                ->label('Vaga')
                ->required()
                ->disabled(fn(Get $get): bool => blank($get('process_id')))
                ->columnSpanFull()
                ->options(function (Get $get) {
                    $processId = $get('process_id');

                    if (!$processId) return [];

                    return Position::where('process_id', $processId)
                        ->get()
                        ->mapWithKeys(fn($p) => [$p->id => "{$p->code} - {$p->description}"])
                        ->toArray();
                }),
        ];
    }

    // --------------------------
    // SECTION: Tipo de Vaga
    // --------------------------
    private static function getTipoVagaSection(): array
    {
        return [
            Select::make('quota_id')
                ->label('Tipo da vaga')
                ->disabled(fn($get) => blank($get('process_id')))
                ->required()
                ->columnSpanFull()
                ->live()
                ->options(function (callable $get) {
                    $process = Process::find($get('process_id'));
                    return $process?->type->quotas()->get()
                        ->mapWithKeys(fn($p) => [$p->id => "{$p->description}"])
                        ->toArray();
                }),
            AttachmentUpload::make('laudo_medico')
                ->label('Anexar laudo médico')
                ->visible(fn(Get $get): bool => $get('quota_id') === 3)
                ->disk('local')
                ->collection('laudo_medico')
                ->columnSpanFull(),
        ];
    }

    // --------------------------
    // SECTION: Atendimento Especial
    // --------------------------
    private static function getAtendimentoSection(): array
    {
        return [
            Checkbox::make('requires_assistance')
                ->label('Solicitar atendimento especial')
                ->columnSpanFull()
                ->live()
                ->default(false)
                ->live(),

            Textarea::make('assistance_details')
                ->label('Qual Atendimento')
                ->visible(fn(Get $get): bool => $get('requires_assistance'))
                ->required(fn(Get $get) => $get('requires_assistance'))
                ->columnSpanFull(),
        ];
    }

    // --------------------------
    // SECTION: Isenção de Taxa
    // --------------------------
    private static function getIsencaoSection(): array
    {
        return [
            Checkbox::make('pedir_isencao')
                ->label('Solicitar isenção da taxa de inscrição')
                ->visible(fn(Get $get): bool => (bool) $get('has_fee'))
                ->columnSpanFull()
                ->live()
                ->default(false),

            AttachmentUpload::make('isencao_taxa')
                ->label('Comprovante de isenção de taxa')
                ->visible(fn(Get $get): bool => (bool) $get('pedir_isencao'))
                ->disk('local')
                ->collection('isencao_taxa')
                ->columnSpanFull(),
        ];
    }

    // --------------------------
    // SECTION: Termos
    // --------------------------
    private static function getTermosSection(): array
    {
        return [
            Checkbox::make('aceita_termos')
                ->label('Declaro que li e concordo com os termos do edital')
                ->accepted()
                ->columnSpanFull(),
        ];
    }
}

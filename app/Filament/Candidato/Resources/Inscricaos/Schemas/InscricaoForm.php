<?php

namespace App\Filament\Candidato\Resources\Inscricaos\Schemas;

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

class InscricaoForm
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
                            ...self::getAtendimentoSection(),
                            ...self::getPcdSection(),
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
            Select::make('id')
                ->label('Processo Seletivo')
                ->options(fn() => Cache::remember('processos_inscricoes_abertas_options', 60, function () {
                    return Process::inscricoesAbertas()
                        ->get()
                        ->mapWithKeys(fn($p) => [$p->id => "{$p->number} - {$p->title}"])
                        ->toArray();
                }))
                ->placeholder('Selecione um processo seletivo')
                ->searchable()
                ->preload()
                ->required()
                ->columnSpanFull()
                ->live()
                ->afterStateUpdated(function (callable $set, $state) {
                    $processo = Process::find($state);
                    $set('has_fee_exemption', $processo?->has_fee_exemption);
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
            Group::make()
                ->visible(fn(Get $get) => (bool) $get('id'))
                ->schema(function (Get $get) {
                    $attachments = (array) $get('attachment_fields');

                    return [
                        Fieldset::make('Documentos Requeridos')
                            ->schema(
                                collect($attachments)->map(function ($item, $index) {
                                    $label = $item['item'] ?? "documento_{$index}";
                                    $fieldKey = 'documentos_requeridos_' . Str::slug("{$index}_{$label}");

                                    return SpatieMediaLibraryFileUpload::make($fieldKey)
                                        ->label(fn() => $label)
                                        ->disk('local')
                                        ->maxFiles(1)
                                        ->required()
                                        ->rules(['file', 'mimes:pdf', 'max:2048'])
                                        ->acceptedFileTypes(['application/pdf'])
                                        ->getUploadedFileNameForStorageUsing(function (TemporaryUploadedFile $file) use ($label): string {
                                            return Str::slug($label) . '.' . $file->getClientOriginalExtension();
                                        });
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
            Select::make('idinscricao_vaga')
                ->label('Vaga')
                ->required()
                ->disabled(fn(Get $get): bool => !filled($get('id')))
                ->columnSpanFull()
                ->options(function (Get $get) {
                    $id = $get('id');
                    if (! $id) return [];

                    return Position::where('id', $id)
                        ->get()
                        ->mapWithKeys(fn($v) => [$v->idinscricao_vaga => "{$v->code} - {$v->description}"])
                        ->toArray();
                }),
        ];
    }

    // --------------------------
    // SECTION: Atendimento Especial
    // --------------------------
    private static function getAtendimentoSection(): array
    {
        return [
            Select::make('requires_assistance')
                ->label('Precisa de Atendimento Especial?')
                ->helperText('Ex: apoio para leitura ou escrita, mobiliário adaptado, etc')
                ->required()
                ->columnSpanFull()
                ->default('N')
                ->options(['N' => 'Não', 'S' => 'Sim'])
                ->live(),

            Textarea::make('assistance_details')
                ->visible(fn(Get $get): bool => $get('requires_assistance') === 'S')
                ->required(fn(Get $get) => $get('requires_assistance') === 'S')
                ->columnSpanFull(),
        ];
    }

    // --------------------------
    // SECTION: PCD
    // --------------------------
    private static function getPcdSection(): array
    {
        return [
            Checkbox::make('pcd')
                ->label('Concorrer nas vagas reservadas a Pessoas com Deficiência (PCD)')
                ->columnSpanFull()
                ->live()
                ->default(false),

            SpatieMediaLibraryFileUpload::make('laudo_medico')
                ->label('Anexar laudo médico')
                ->visible(fn(Get $get): bool => (bool) $get('pcd'))
                ->required(fn(Get $get) => (bool) $get('pcd'))
                ->maxFiles(1)
                ->disk('local')
                ->collection('laudo_medico')
                ->rules(['file', 'mimes:pdf', 'max:2048'])
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
                ->visible(fn(Get $get): bool => (bool) $get('has_fee_exemption'))
                ->columnSpanFull()
                ->live()
                ->default(false),

            SpatieMediaLibraryFileUpload::make('isencao_taxa')
                ->label('Comprovante de isenção de taxa')
                ->visible(fn(Get $get): bool => (bool) $get('pedir_isencao'))
                ->required(fn(Get $get): bool => (bool) $get('pedir_isencao'))
                ->maxFiles(1)
                ->disk('local')
                ->collection('isencao_taxa')
                ->rules(['file', 'mimes:pdf', 'max:2048'])
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

<?php

namespace App\Filament\Candidato\Resources;

use App\Filament\Candidato\Resources\InscricaoResource\Pages;
use App\Models\Inscricao;
use App\Models\InscricaoVaga;
use App\Models\ProcessoSeletivo;
use Filament\Forms;
use Filament\Forms\Components\Fieldset;
use Filament\Forms\Components\Grid;
use Filament\Forms\Components\Group;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\SpatieMediaLibraryFileUpload;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\Checkbox;
use Filament\Forms\Components\Placeholder;
use Filament\Forms\Get;
use Filament\Infolists\Components\Section;
use Filament\Infolists\Components\TextEntry;
use Filament\Infolists\Infolist;
use Filament\Navigation\NavigationItem;
use Filament\Resources\Resource;
use Filament\Tables\Table;
use Filament\Tables\Columns\Layout\Stack;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Str;
use Livewire\Features\SupportFileUploads\TemporaryUploadedFile;

class InscricaoResource extends Resource
{
    protected static ?string $model = Inscricao::class;
    protected static ?string $modelLabel = 'Inscrição';
    protected static ?string $pluralModelLabel = 'Minhas Inscrições';
    protected static ?string $slug = 'inscricoes';
    protected static ?string $navigationIcon = 'heroicon-o-list-bullet';
    protected static ?string $navigationGroup = 'Área do Candidato';
    protected static ?int $navigationSort = 1;

    public static function infolist(Infolist $infolist): Infolist
    {
        return $infolist
            ->schema([
                Section::make('DADOS PESSOAIS')
                    ->columns(3)
                    ->schema([
                        TextEntry::make('inscricao_pessoa.nome')->label('Nome'),
                        TextEntry::make('inscricao_pessoa.cpf')->label('CPF'),
                        TextEntry::make('inscricao_pessoa.ci')->label('RG'),
                    ]),

                Section::make('DADOS DA INSCRIÇÃO')
                    ->columns(3)
                    ->schema([
                        TextEntry::make('cod_inscricao')->label('Inscrição'),
                        TextEntry::make('processo_seletivo.titulo')->label('Processo Seletivo'),
                        TextEntry::make('inscricao_vaga.descricao')->label('Vaga'),
                        TextEntry::make('tipo_vaga.descricao')->label('Tipo de Vaga'),
                    ]),

                Section::make('ATENDIMENTO ESPECIAL')
                    ->columns(3)
                    ->schema([
                        TextEntry::make('necessita_atendimento')
                            ->label('')
                            ->badge()
                            ->colors([
                                'success' => 'S',
                                'gray' => 'N',
                            ])
                            ->formatStateUsing(fn($state) => $state === 'S' ? 'solicitado' : 'não solicitado'),

                        TextEntry::make('qual_atendimento')
                            ->label('Qual Atendimento')
                            ->visible(fn($record) => $record->necessita_atendimento === 'S'),
                    ]),
            ]);
    }

    public static function form(Forms\Form $form): Forms\Form
    {
        return $form
            ->schema([
                Grid::make([
                    'default' => 1,
                    'lg' => 2,
                ])->schema([

                    // Left column (main inputs)
                    Grid::make()
                        ->columnSpan([
                            'default' => 1,
                            'lg' => 1,
                        ])
                        ->schema([

                            Select::make('idprocesso_seletivo')
                                ->label('Processo Seletivo')
                                ->options(function () {
                                    // cache shortlist for a short time to reduce DB load on big lists
                                    return Cache::remember('processos_inscricoes_abertas_options', 60, function () {
                                        return ProcessoSeletivo::inscricoesAbertas()
                                            ->get()
                                            ->mapWithKeys(function ($processo) {
                                                return [$processo->idprocesso_seletivo => "{$processo->numero} - {$processo->titulo}"];
                                            })->toArray();
                                    });
                                })
                                ->placeholder('Selecione um processo seletivo')
                                ->searchable()
                                ->preload()
                                ->required()
                                ->columnSpanFull()
                                ->live()
                                ->afterStateUpdated(function (callable $set, $state) {
                                    // Ensure anexos is always set as an array (avoid null/string issues)
                                    $processo = ProcessoSeletivo::find($state);
                                    $set('possui_isencao', $processo?->possui_isencao);
                                    $anexos = (array) ($processo?->anexos ?? []);
                                    $set('anexos', $anexos);
                                }),

                            // Group wraps the Fieldset and is hidden entirely when no processo selected
                            Group::make()
                                ->visible(fn(Get $get) => (bool) $get('idprocesso_seletivo'))
                                ->schema(function (Get $get) {
                                    $anexos = (array) $get('anexos'); // make sure it's array
                                    // Build fields safely. If no anexos, return empty array.
                                    return [
                                        Fieldset::make('Documentos Requeridos')
                                            ->schema(
                                                collect($anexos)->map(function ($item, $index) {
                                                    // sanitize/slugify an identifier to avoid invalid Livewire state keys
                                                    $label = $item['item'] ?? "documento_{$index}";
                                                    $fieldKey = 'documentos_requeridos_' . Str::slug("{$index}_{$label}");

                                                    return SpatieMediaLibraryFileUpload::make($fieldKey)
                                                        ->label(fn() => $label)
                                                        ->disk('local')
                                                        ->maxFiles(1)
                                                        ->required()
                                                        // Validate conditionally on the presence requirement (if a process expects it)
                                                        ->rules(['file', 'mimes:pdf', 'max:2048']) // max = kilobytes
                                                        ->acceptedFileTypes(['application/pdf'])
                                                        ->getUploadedFileNameForStorageUsing(function (TemporaryUploadedFile $file) use ($label): string {
                                                            return Str::slug($label) . '.' . $file->getClientOriginalExtension();
                                                        });
                                                })->toArray()
                                            )
                                    ];
                                })
                                ->columnSpanFull(),

                            Select::make('idinscricao_vaga')
                                ->label('Vaga')
                                ->required()
                                ->disabled(fn(Get $get): bool => !filled($get('idprocesso_seletivo')))
                                ->columnSpanFull()
                                ->options(function (Get $get) {
                                    $processoSeletivoId = $get('idprocesso_seletivo');

                                    if (! $processoSeletivoId) {
                                        return [];
                                    }

                                    return InscricaoVaga::where('idprocesso_seletivo', $processoSeletivoId)
                                        ->get()
                                        ->mapWithKeys(function ($vaga) {
                                            return [$vaga->idinscricao_vaga => "{$vaga->codigo} - {$vaga->descricao}"];
                                        })
                                        ->toArray();
                                }),

                            Select::make('necessita_atendimento')
                                ->label('Precisa de Atendimento Especial?')
                                ->helperText('Ex: apoio para leitura ou escrita, mobiliário adaptado, etc')
                                ->required()
                                ->columnSpanFull()
                                ->default('N')
                                ->options([
                                    'N' => 'Não',
                                    'S' => 'Sim',
                                ])
                                ->live(),

                            Textarea::make('qual_atendimento')
                                ->visible(fn(Get $get): bool => $get('necessita_atendimento') === 'S')
                                ->required(fn(Get $get) => $get('necessita_atendimento') === 'S')
                                ->columnSpanFull(),

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

                            Checkbox::make('pedir_isencao')
                                ->label('Solicitar isenção da taxa de inscrição')
                                ->visible(fn(Get $get): bool => (bool) $get('possui_isencao'))
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

                            Checkbox::make('aceita_termos')
                                ->label('Declaro que li e concordo com os termos do edital')
                                ->required()
                                ->columnSpanFull(),
                        ]),

                    // Right column - optional area for context/help/preview (instead of empty placeholder)
                ]),
            ]);
    }

    public static function table(Table $table): Table
    {
        // Use a safe query closure which will not blow up if the kandidat is not logged in.
        return $table
            ->query(function (): Builder {
                $guardId = Auth::guard('candidato')->id();
                $query = Inscricao::query()->orderBy('idinscricao', 'desc');

                // If there's no logged candidate, return an empty result set (don't throw)
                if (! $guardId) {
                    // This forces no results but keeps a valid Builder instance
                    return $query->whereRaw('0 = 1');
                }

                return $query->where('idinscricao_pessoa', $guardId);
            })
            ->heading('Inscrições realizadas')
            ->description('Use a caixa de busca para filtrar uma informação.')
            ->columns([
                Stack::make([
                    \Filament\Tables\Columns\TextColumn::make('cod_inscricao')
                        ->label('Código')
                        ->searchable()
                        ->weight('bold')
                        ->size('sm'),

                    \Filament\Tables\Columns\TextColumn::make('processo_seletivo.titulo')
                        ->label('Processo Seletivo')
                        ->searchable()
                        ->size('sm')
                        ->color('gray'),

                    \Filament\Tables\Columns\TextColumn::make('inscricao_vaga.codigo')
                        ->label('Cód. Vaga')
                        ->size('sm')
                        ->color('gray'),

                    \Filament\Tables\Columns\TextColumn::make('inscricao_vaga.descricao')
                        ->label('Descrição')
                        ->size('sm')
                        ->color('gray'),

                    \Filament\Tables\Columns\TextColumn::make('tipo_vaga.descricao')
                        ->label('Tipo')
                        ->size('sm')
                        ->color('gray'),
                ]),
            ])
            ->actions([
                \Filament\Tables\Actions\ViewAction::make(),
            ]);
    }

    public static function getRelations(): array
    {
        return [];
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListInscricaos::route('/'),
            'create' => Pages\CreateInscricao::route('/create'),
            'view' => Pages\ViewInscricao::route('/{record}'),
        ];
    }

    public static function getNavigationItems(): array
    {
        return [
            NavigationItem::make(static::getNavigationLabel())
                ->group(static::getNavigationGroup())
                ->parentItem(static::getNavigationParentItem())
                ->icon(static::getNavigationIcon())
                ->activeIcon(static::getActiveNavigationIcon())
                ->isActiveWhen(fn() => request()->routeIs(static::getRouteBaseName() . '.*')
                    && !request()->routeIs(static::getRouteBaseName() . '.create'))
                ->badge(static::getNavigationBadge(), color: static::getNavigationBadgeColor())
                ->badgeTooltip(static::getNavigationBadgeTooltip())
                ->sort(static::getNavigationSort())
                ->url(static::getNavigationUrl()),
        ];
    }
}

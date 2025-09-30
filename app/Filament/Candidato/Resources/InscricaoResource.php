<?php

namespace App\Filament\Candidato\Resources;

use App\Filament\Candidato\Resources\InscricaoResource\Pages;
use App\Filament\Candidato\Resources\InscricaoResource\RelationManagers;
use App\Filament\Candidato\Resources\InscricaoResource\RelationManagers\RecursosRelationManager;
use App\Models\Inscricao;
use App\Models\InscricaoVaga;
use App\Models\ProcessoSeletivo;
use Filament\Forms;
use Filament\Forms\Components\Group;
use Filament\Forms\Components\SpatieMediaLibraryFileUpload;
use Filament\Forms\Form;
use Filament\Forms\Get;
use Filament\Forms\Set;
use Filament\Infolists\Components\Actions;
use Filament\Infolists\Components\Actions\Action;
use Filament\Infolists\Components\IconEntry;
use Filament\Infolists\Components\Section;
use Filament\Infolists\Components\TextEntry;
use Filament\Infolists\Components\TextEntry\TextEntrySize;
use Filament\Infolists\Infolist;
use Filament\Navigation\NavigationItem;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Columns\Layout\Split;
use Filament\Tables\Columns\Layout\Stack;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use Illuminate\Support\Facades\Auth;
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

    protected const documentos = [
        'rg',
        'cpf',
    ];

    public static function infolist(InfoList $infolist): Infolist
    {
        return $infolist
            ->schema([
                Section::make('DADOS PESSOAIS')
                    ->columns(3)
                    ->schema([
                        TextEntry::make('inscricao_pessoa.nome')
                            ->label('Nome'),
                        TextEntry::make('inscricao_pessoa.cpf')
                            ->label('CPF'),
                        TextEntry::make('inscricao_pessoa.ci')
                            ->label('RG'),
                    ]),

                Section::make('DADOS DA INSCRIÇÃO')
                    ->columns(3)
                    ->schema([
                        TextEntry::make('cod_inscricao')
                            ->label('Inscrição'),
                        TextEntry::make('processo_seletivo.titulo')
                            ->label('Processo Seletivo'),
                        TextEntry::make('inscricao_vaga.descricao')
                            ->label('Vaga'),
                        TextEntry::make('tipo_vaga.descricao')
                            ->label('Tipo de Vaga'),

                        // TextEntry::make('necessita_atendimento')
                        //     ->label('Necessita Atendimento Especial')
                        //     ->badge(),

                        // TextEntry::make('qual_atendimento')
                        //     ->label('Qual Atendimento')
                        //     ->visible(fn($record) => $record->necessita_atendimento === 'S'),
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

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Grid::make([
                    'default' => 1,    // 1 column on small screens
                    'lg' => 2          // 2 columns on large screens
                ])
                    ->schema([
                        Forms\Components\Grid::make()
                            ->columnSpan([
                                'default' => 1,
                                'lg' => 1
                            ])
                            ->schema([
                                Forms\Components\Select::make('idprocesso_seletivo')
                                    ->label('Processo Seletivo')
                                    ->options(function () {
                                        return ProcessoSeletivo::inscricoesAbertas()
                                            ->get()
                                            ->mapWithKeys(function ($processo) {
                                                return [$processo->idprocesso_seletivo => "{$processo->numero} - {$processo->titulo}"];
                                            });
                                    })
                                    ->placeholder('Selecione um processo seletivo')
                                    ->searchable()
                                    ->preload()
                                    ->required()
                                    ->columnSpanFull()
                                    ->live(),

                                Group::make([
                                    ...collect(self::documentos)->map(function ($item, $index) {
                                        return SpatieMediaLibraryFileUpload::make("documentos_requeridos_$index")
                                            ->label($item)
                                            ->disk('local')
                                            ->maxFiles(1)
                                            ->rules(['file', 'mimes:pdf', 'max:10240'])
                                            ->getUploadedFileNameForStorageUsing(function (TemporaryUploadedFile $file) use ($item): string {
                                                return $item . '.' . $file->getClientOriginalExtension();
                                            })
                                            ->columnSpan(1);
                                    })
                                ])->columns(2),

                                // SpatieMediaLibraryFileUpload::make('documentos_requeridos')
                                //     ->label('Anexar documentos')
                                //     ->helperText('* Este Processo Seletivo requer o envio de documentação comprobatória no momento da inscrição. Anexe aqui todos os documentos requeridos em um único arquivo em formato PDF.')
                                //     ->hint('* Formato PDF')
                                //     ->visible(function (Get $get): bool {
                                //         $id = $get('idprocesso_seletivo');
                                //         if (!$id) return false;

                                //         $processoSeletivo = ProcessoSeletivo::where('idprocesso_seletivo', $id)->first();
                                //         return $processoSeletivo?->requer_anexos;
                                //     })
                                //     ->required()
                                //     ->maxFiles(1)
                                //     ->disk('local')
                                //     ->rules(['file', 'mimes:pdf', 'max:10240'])
                                //     ->columnSpanFull(),

                                Forms\Components\Select::make('idinscricao_vaga')
                                    ->label('Vaga')
                                    ->required()
                                    ->disabled(fn(Get $get): bool => !filled($get('idprocesso_seletivo')))
                                    ->columnSpanFull()
                                    ->options(function (Get $get) {
                                        $processoSeletivoId = $get('idprocesso_seletivo');

                                        return $processoSeletivoId
                                            ? InscricaoVaga::where('idprocesso_seletivo', $processoSeletivoId)
                                            ->get()
                                            ->mapWithKeys(function ($vaga) {
                                                return [$vaga->idinscricao_vaga => "{$vaga->codigo} - {$vaga->descricao}"];
                                            })
                                            : [];
                                    }),

                                // Forms\Components\Select::make('idtipo_vaga')
                                //     ->relationship('tipo_vaga', 'descricao')
                                //     ->columnSpanFull()
                                //     ->required(),

                                Forms\Components\Select::make('necessita_atendimento')
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

                                Forms\Components\Textarea::make('qual_atendimento')
                                    ->visible(fn(Get $get): bool => $get('necessita_atendimento') === 'S')
                                    ->required()
                                    ->columnSpanFull(),

                                Forms\Components\Checkbox::make('pcd')
                                    ->label('Concorrer nas vagas reservadas a Pessoas com Decifiência (PCD)')
                                    ->columnSpanFull()
                                    ->live()
                                    ->default(false),

                                SpatieMediaLibraryFileUpload::make('laudo_medico')
                                    ->label('Anexar laudo médico')
                                    ->visible(fn(Get $get): bool => $get('pcd'))
                                    ->required()
                                    ->maxFiles(1)
                                    ->disk('local')
                                    ->collection('laudo_medico')
                                    ->rules(['file', 'mimes:pdf', 'max:10240'])
                                    ->columnSpanFull(),

                                Forms\Components\Checkbox::make('aceita_termos')
                                    ->label('Declaro que li e concordo com os termos do edital')
                                    ->required()
                                    ->columnSpanFull(),
                            ]),


                        // Empty second column for large screens
                        Forms\Components\Placeholder::make('')->columnSpan(1)->visible(fn() => true),
                    ])
            ]);
    }


    public static function table(Table $table): Table
    {
        return $table
            // Filtra apenas as inscrições do candidato
            ->query(Inscricao::where('idinscricao_pessoa', Auth::guard('candidato')->id())->orderBy('idinscricao', 'desc'))
            ->heading('Inscrições realizadas')
            ->description('Use a caixa de busca para filtrar uma informação.')
            ->columns([
                Stack::make([
                    Tables\Columns\TextColumn::make('cod_inscricao')
                        ->label('Código')
                        ->searchable()
                        ->weight('bold') // Makes the text bold
                        ->size('sm'),  // Keeps default size or slightly larger

                    Tables\Columns\TextColumn::make('processo_seletivo.titulo')
                        ->label('Processo Seletivo')
                        ->searchable()
                        ->size('sm')
                        ->color('gray'),

                    Tables\Columns\TextColumn::make('inscricao_vaga.codigo')
                        ->label('Cód. Vaga')
                        ->size('sm')
                        ->color('gray'),

                    Tables\Columns\TextColumn::make('inscricao_vaga.descricao')
                        ->label('Descrição')
                        ->size('sm')
                        ->color('gray'),

                    Tables\Columns\TextColumn::make('tipo_vaga.descricao')
                        ->label('Tipo')
                        ->size('sm')
                        ->color('gray'),
                ]),
            ])
            ->filters([
                //
            ])
            ->actions([
                Tables\Actions\ViewAction::make(),
                // Tables\Actions\EditAction::make(),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    // Tables\Actions\DeleteBulkAction::make(),
                ]),
            ]);
    }

    public static function getRelations(): array
    {
        return [
            //
            // RecursosRelationManager::class,
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListInscricaos::route('/'),
            'create' => Pages\CreateInscricao::route('/create'),
            'view' => Pages\ViewInscricao::route('/{record}'),
            // 'edit' => Pages\EditInscricao::route('/{record}/edit'),
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

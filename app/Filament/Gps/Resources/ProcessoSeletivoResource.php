<?php

namespace App\Filament\Gps\Resources;

use Filament\Schemas\Schema;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Components\Group;
use Filament\Schemas\Components\Fieldset;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\SelectFilter;
use Filament\Actions\EditAction;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;
use App\Filament\Gps\Resources\ProcessoSeletivoResource\Pages\ListProcessoSeletivos;
use App\Filament\Gps\Resources\ProcessoSeletivoResource\Pages\CreateProcessoSeletivo;
use App\Filament\Gps\Resources\ProcessoSeletivoResource\Pages;
use App\Filament\Gps\Resources\ProcessoSeletivoResource\Pages\EditProcessoSeletivo;
use App\Filament\Gps\Resources\ProcessoSeletivoResource\Pages\ManageAnexos;
use App\Filament\Gps\Resources\ProcessoSeletivoResource\Pages\ManageAvaliadores;
use App\Filament\Gps\Resources\ProcessoSeletivoResource\Pages\ManageEtapaRecurso;
use App\Filament\Gps\Resources\ProcessoSeletivoResource\Pages\ManageInscritos;
use App\Filament\Gps\Resources\ProcessoSeletivoResource\Pages\ManageRecursos;
use App\Filament\Gps\Resources\ProcessoSeletivoResource\Pages\ManageVagas;
use App\Filament\Gps\Resources\ProcessoSeletivoResource\RelationManagers;
use App\Filament\Gps\Resources\ProcessoSeletivoResource\RelationManagers\AnexosRelationManager;
use App\Filament\Gps\Resources\ProcessoSeletivoResource\RelationManagers\InscricaoVagaRelationManager;
use App\Filament\Gps\Resources\ProcessoSeletivoResource\RelationManagers\InscricoesRelationManager;
use App\Filament\Gps\Resources\ProcessoSeletivoResource\RelationManagers\RecursosRelationManager;
use App\Models\ProcessoSeletivo;
use Carbon\Carbon;
use Filament\Forms;
use Filament\Forms\Components\Checkbox;
use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\Repeater;
use Filament\Forms\Components\RichEditor;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Tabs;
use Filament\Forms\Components\Tabs\Tab;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Set;
use Filament\Pages\SubNavigationPosition;
use Filament\Resources\Pages\Page;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Columns\Layout\Stack;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Str;

class ProcessoSeletivoResource extends Resource
{
    protected static ?string $model = ProcessoSeletivo::class;
    protected static ?string $modelLabel = 'Processo Seletivo';
    protected static ?string $pluralModelLabel = 'Processos Seletivos';
    protected static ?string $slug = 'processos';
    protected static string | \BackedEnum | null $navigationIcon = 'heroicon-o-folder';
    protected static string | \UnitEnum | null $navigationGroup = 'Gerenciar';
    protected static ?int $navigationSort = 1;
    protected static ?string $recordTitleAttribute = 'titulo';
    protected static int $globalSearchResultsLimit = 20;

    public static function canAccess(): bool
    {
        return Auth::user()->hasRole('gestor|admin');
    }

    public static function getGlobalSearchResultDetails(Model $record): array
    {
        return [
            'Edital' => $record->numero,
        ];
    }
    public static function getGlobalSearchEloquentQuery(): Builder
    {
        return parent::getGlobalSearchEloquentQuery()->orderBy('data_publicacao_inicio', 'desc');
    }

    public static function form(Schema $schema): Schema
    {
        return $schema
            ->components([
                Section::make([
                    Group::make([
                        TextInput::make('titulo')
                            ->label('Título')
                            ->columnSpan(2)
                            ->required(),
                        Select::make('idprocesso_seletivo_tipo')
                            ->label('Tipo')
                            ->relationship('tipo', 'descricao')
                            ->required(),
                    ])->columns(3),

                    Group::make([
                        TextInput::make('numero')
                            ->disabledOn('edit')
                            ->placeholder('Ex: 01/2025')
                            ->required(),
                        DatePicker::make('data_criacao')
                            ->label('Data do Edital')
                            ->required(),
                        Select::make('publicado')
                            ->required()
                            ->options([
                                'S' => 'Sim',
                                'N' => 'Não'
                            ]),
                    ])->columns(3),

                    RichEditor::make('descricao')
                        ->required()
                        ->label('Descrição'),

                    Group::make([
                        Fieldset::make('Período de Publicação')
                            ->schema([
                                DatePicker::make('data_publicacao_inicio')
                                    ->label('Início')
                                    ->required(),
                                DatePicker::make('data_publicacao_fim')
                                    ->label('Fim')
                                    ->required()
                            ])->columnSpan(1),
                        Fieldset::make('Período de Inscrições')
                            ->schema([
                                DatePicker::make('data_inscricao_inicio')
                                    ->label('Início')
                                    ->required(),
                                DatePicker::make('data_inscricao_fim')
                                    ->label('Fim')
                                    ->required(),
                            ])->columnSpan(1),
                    ])->columns(2),

                    Checkbox::make('possui_isencao')
                        ->label('Possui isenção da taxa de inscrição'),

                    Repeater::make('anexos')
                        ->label('Documentos Requeridos')
                        ->schema([
                            TextInput::make('item')
                                ->label('Nome do Documento')
                                ->required()
                        ])
                        ->cloneable()
                        ->collapsed()
                        ->columnSpanFull()
                        ->minItems(1)
                        ->addActionLabel('Adicionar Documento')
                        ->defaultItems(function ($record) {
                            return $record->anexos ?? [];
                        }),
                ])
            ])->columns(2);
    }


    public static function table(Table $table): Table
    {
        return $table
            ->heading('Consultar Processos Seletivos')
            ->description('Informações sobre todos os processos seletivos cadastrados. Consulte, atualize ou crie um novo registro.')
            ->defaultSort('idprocesso_seletivo', 'desc')
            ->columns([
                //
                TextColumn::make('idprocesso_seletivo')
                    ->label('ID'),
                TextColumn::make('tipo.descricao'),
                TextColumn::make('titulo')
                    ->searchable()
                    ->limit(50),
                TextColumn::make('numero')->searchable(),
                TextColumn::make('publicado')
                    ->badge()
                    ->color(fn($state) => $state === 'S' ? 'success' : 'danger'),
            ])
            ->filters([
                SelectFilter::make('tipo')
                    ->label('Tipo')
                    ->relationship('tipo', 'descricao'),
            ])
            ->recordActions([
                EditAction::make()
            ])
            ->toolbarActions([
                BulkActionGroup::make([
                    DeleteBulkAction::make(),
                ]),
            ]);
    }

    public static function getRelations(): array
    {
        return [
            // AnexosRelationManager::class,
            // InscricaoVagaRelationManager::class,
            // InscricoesRelationManager::class,
            // RecursosRelationManager::class
        ];
    }

    public static function getRecordSubNavigation(Page $page): array
    {
        return $page->generateNavigationItems([
            EditProcessoSeletivo::class,
            ManageAnexos::class,
            ManageInscritos::class,
            ManageVagas::class,
            ManageEtapaRecurso::class,
            // ManageRecursos::class,
            ManageAvaliadores::class,
        ]);
    }

    public static function getPages(): array
    {
        return [
            'index' => ListProcessoSeletivos::route('/'),
            'create' => CreateProcessoSeletivo::route('/create'),
            'edit' => EditProcessoSeletivo::route('/{record}/edit'),
            'anexos' => ManageAnexos::route('/{record}/anexos'),
            'inscritos' => ManageInscritos::route('/{record}/inscritos'),
            'avaliadores' => ManageAvaliadores::route('/{record}/avaliadores'),
            'vagas' => ManageVagas::route('/{record}/vagas'),
            // 'recursos' => Pages\ManageRecursos::route('/{record}/recursos'),
            'etapas_recurso' => ManageEtapaRecurso::route('/{record}/etapas_recurso'),
        ];
    }
}

<?php

namespace App\Filament\Gps\Resources;

use App\Filament\Gps\Resources\ProcessoSeletivoResource\Pages;
use App\Filament\Gps\Resources\ProcessoSeletivoResource\Pages\EditProcessoSeletivo;
use App\Filament\Gps\Resources\ProcessoSeletivoResource\Pages\ManageAnexos;
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
use Filament\Forms\Components\Fieldset;
use Filament\Forms\Components\Group;
use Filament\Forms\Components\RichEditor;
use Filament\Forms\Components\Section;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Tabs;
use Filament\Forms\Components\Tabs\Tab;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Form;
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
use Illuminate\Support\Str;

class ProcessoSeletivoResource extends Resource
{
    protected static ?string $model = ProcessoSeletivo::class;
    protected static ?string $modelLabel = 'Processo Seletivo';
    protected static ?string $pluralModelLabel = 'Processos Seletivos';
    protected static ?string $slug = 'processos';
    protected static ?string $navigationIcon = 'heroicon-o-folder';
    protected static ?string $navigationGroup = 'Gerenciar';
    protected static ?int $navigationSort = 1;
    protected static ?string $recordTitleAttribute = 'titulo';
    protected static int $globalSearchResultsLimit = 20;
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

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Section::make([
                    Group::make([
                        TextInput::make('titulo')
                            ->label('Título')
                            ->required(),
                        Select::make('idprocesso_seletivo_tipo')
                            ->label('Tipo')
                            ->relationship('tipo', 'descricao')
                            ->required(),
                        RichEditor::make('descricao')
                            ->required()
                            ->label('Descrição'),
                        Checkbox::make('requer_anexos')
                            ->label('Requer documentos anexos na inscrição?'),

                        Select::make('publicado')
                            ->required()
                            ->options([
                                'S' => 'Sim',
                                'N' => 'Não'
                            ]),
                        TextInput::make('numero')
                            ->disabledOn('edit')
                            ->placeholder('Ex: 01/2025')
                            ->live(onBlur: true)
                            ->afterStateUpdated(fn(Set $set, $state) => $set('diretorio',  str_replace('/', '_', $state)))
                            ->required(),
                        TextInput::make('diretorio')
                            ->disabledOn('edit')
                            ->label('Diretório')
                            ->required()
                            ->unique('processo_seletivo', 'diretorio', ignoreRecord: true),
                        Select::make('psu')
                            ->label('PSU')
                            ->required()
                            ->options([
                                'S' => 'Sim',
                                'N' => 'Não'
                            ]),
                    ]),
                    Group::make([
                        DatePicker::make('data_criacao')
                            ->label('Data do Edital')
                            ->required(),
                        Fieldset::make('Período de Publicação')
                            ->schema([
                                DatePicker::make('data_publicacao_inicio')
                                    ->label('Início')
                                    ->required(),
                                DatePicker::make('data_publicacao_fim')
                                    ->label('Fim')
                                    ->required()
                            ]),
                        Fieldset::make('Período de Inscrições')
                            ->schema([
                                DatePicker::make('data_inscricao_inicio')
                                    ->label('Início')
                                    ->required(),
                                DatePicker::make('data_inscricao_fim')
                                    ->label('Fim')
                                    ->required(),
                            ]),
                    ]),
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
                Tables\Columns\TextColumn::make('idprocesso_seletivo')
                    ->label('ID'),
                Tables\Columns\TextColumn::make('tipo.descricao'),
                Tables\Columns\TextColumn::make('titulo')
                    ->searchable()
                    ->limit(50),
                Tables\Columns\TextColumn::make('numero')->searchable(),
                Tables\Columns\TextColumn::make('publicado')
                    ->badge()
                    ->color(fn($state) => $state === 'S' ? 'success' : 'danger'),
            ])
            ->filters([
                //
            ])
            ->actions([
                Tables\Actions\EditAction::make()
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
            ManageRecursos::class,
        ]);
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListProcessoSeletivos::route('/'),
            'create' => Pages\CreateProcessoSeletivo::route('/create'),
            'edit' => Pages\EditProcessoSeletivo::route('/{record}/edit'),
            'anexos' => Pages\ManageAnexos::route('/{record}/anexos'),
            'inscritos' => Pages\ManageInscritos::route('/{record}/inscritos'),
            'vagas' => Pages\ManageVagas::route('/{record}/vagas'),
            'recursos' => Pages\ManageRecursos::route('/{record}/recursos'),
            'etapas_recurso' => Pages\ManageEtapaRecurso::route('/{record}/etapas_recurso'),
        ];
    }
}

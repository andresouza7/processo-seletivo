<?php

namespace App\Filament\App\Resources\ProcessoSeletivos;

use Filament\Schemas\Schema;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Components\Actions;
use Filament\Actions\Action;
use Filament\Actions\BulkActionGroup;
use App\Filament\App\Resources\ProcessoSeletivos\Pages\ListProcessoSeletivos;
use App\Filament\App\Resources\ProcessoSeletivos\Pages\ViewProcessoSeletivo;
use App\Filament\App\Resources\ProcessoSeletivoResource\Pages;
use App\Filament\App\Resources\ProcessoSeletivos\RelationManagers\AnexosRelationManager;
use App\Models\ProcessoSeletivo;
use Carbon\Carbon;
use Filament\Infolists\Components\Group;
use Filament\Infolists\Components\Split;
use Filament\Infolists\Components\TextEntry;
use Filament\Pages\Page;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Columns\Layout\Split as TableSplit;
use Filament\Tables\Columns\Layout\Stack;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Table;
use HtmlHelper;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use Illuminate\Support\HtmlString;

class ProcessoSeletivoResource extends Resource
{
    protected static ?string $model = ProcessoSeletivo::class;
    protected static string | \BackedEnum | null $navigationIcon = 'heroicon-o-rectangle-stack';
    protected static bool $shouldRegisterNavigation = false;
    protected static ?string $recordTitleAttribute = 'titulo';
    protected static int $globalSearchResultsLimit = 20;

    public static function getGlobalSearchResultDetails(Model $record): array
    {
        return [
            'Edital' => $record->numero,
        ];
    }
    public static function getGloballySearchableAttributes(): array
    {
        return ['titulo', 'numero'];
    }
    public static function getGlobalSearchEloquentQuery(): Builder
    {
        return parent::getGlobalSearchEloquentQuery()->orderBy('data_publicacao_inicio', 'desc');
    }

    public static function getEloquentQuery(): Builder
    {
        $query = parent::getEloquentQuery()
            ->withoutGlobalScopes([
                SoftDeletingScope::class,
            ]);

        // Access the status query parameter directly from the request
        $status = request()->query('status');

        if ($status) {
            switch ($status) {
                case 'inscricoes_abertas':
                    $query->inscricoesAbertas();
                    break;
                case 'em_andamento':
                    $query->emAndamento();
                    break;
                case 'finalizados':
                    $query->finalizados();
                    break;
            }
        }

        $query->where('publicado', 'S');

        return $query;
    }

    public static function form(Schema $schema): Schema
    {
        return $schema
            ->components([
                //
            ]);
    }

    public static function infolist(Schema $schema): Schema
    {
        return $schema
            ->components([
                Section::make([
                    TextEntry::make('titulo')
                        ->label('Nome')
                        ->extraAttributes(['class' => 'font-semibold text-gray-700']),
                    TextEntry::make('numero')
                        ->label('Número')
                        ->extraAttributes(['class' => 'font-semibold text-gray-600']),
                    TextEntry::make('data_inscricao_inicio')
                        ->label('Período de Inscrições')
                        ->icon('heroicon-o-calendar')
                        ->formatStateUsing(fn($record) => sprintf(
                            "%s a %s",
                            Carbon::parse($record->data_inscricao_inicio)->format('d/m/Y'),
                            Carbon::parse($record->data_inscricao_fim)->format('d/m/Y')
                        ))
                        ->extraAttributes(['class' => 'font-semibold text-gray-700']),
                    Actions::make([
                        Action::make('createInscricao')
                            ->visible(fn($record) => $record->aceita_inscricao)
                            ->label('Realizar Inscrição')
                            ->url(route('filament.candidato.resources.inscricoes.create'))
                            ->button()
                            ->color('primary'),
                        Action::make('createRecurso')
                            ->visible(fn($record) => $record->aceita_recurso)
                            ->label('Recursos')
                            ->url(fn($record) => $record->link_recurso)
                            ->button()
                            ->color('primary')
                    ]),
                    // TextEntry::make('descricao')->html()
                    TextEntry::make('descricao')
                        ->hiddenLabel()
                        ->formatStateUsing(fn(string $state): HtmlString => new HtmlString(HtmlHelper::sliceBodyContent($state))),

                ])
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->heading('Consultar Processos Seletivos')
            ->description('Utilize o campo de pesquisa para filtrar uma informação')
            ->defaultSort('data_publicacao_inicio', 'desc')
            ->columns([
                //
                Stack::make([
                    TableSplit::make([
                        TextColumn::make('numero')
                            ->searchable()
                            ->formatStateUsing(fn($record) => sprintf(
                                "<div class='text-xs font-semibold flex gap-3'><span>Publicação: %s</span><span>Nº do Edital: %s</span></div>",
                                Carbon::parse($record->data_criacao)->format('d/m/Y'),
                                $record->numero
                            ))->html()
                            ->color('gray')
                            ->grow(false),
                    ]),
                    TextColumn::make('titulo')
                        ->color('primary')
                        ->searchable()
                        ->limit()
                ])

            ])
            ->filters([
                SelectFilter::make('status')
                    ->label('Status')
                    ->options([
                        'inscricoes_abertas' => 'Inscrições Abertas',
                        'em_andamento' => 'Em Andamento',
                        'finalizados' => 'Finalizados',
                    ])
                    ->query(function (Builder $query, array $data): Builder {
                        return match ($data['value'] ?? null) {
                            'inscricoes_abertas' => $query->inscricoesAbertas(),
                            'em_andamento' => $query->emAndamento(),
                            'finalizados' => $query->finalizados(),
                            default => $query,
                        };
                    }),
            ])
            ->recordActions([
                // Tables\Actions\ViewAction::make(),
                // Tables\Actions\EditAction::make(),
            ])
            ->toolbarActions([
                BulkActionGroup::make([
                    // Tables\Actions\DeleteBulkAction::make(),
                ]),
            ]);
    }

    public static function getRelations(): array
    {
        return [
            //
            AnexosRelationManager::class,
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => ListProcessoSeletivos::route('/'),
            // 'create' => Pages\CreateProcessoSeletivo::route('/create'),
            'view' => ViewProcessoSeletivo::route('/{record}'),
            // 'edit' => Pages\EditProcessoSeletivo::route('/{record}/edit'),
        ];
    }
}

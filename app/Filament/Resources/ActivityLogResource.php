<?php

namespace App\Filament\Resources;

use App\Filament\Exports\ActivityExporter;
use App\Filament\Resources\ActivityLogResource\Pages;
use App\Models\InscricaoPessoa;
use App\Models\User;
use Filament\Forms\Components\KeyValue;
use Filament\Forms\Components\Placeholder;
use Filament\Forms\Components\Section;
use Filament\Forms\Components\Split;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Form;
use Filament\Tables\Actions\Action;
use Filament\Tables\Actions\ExportAction;
use Filament\Tables\Columns\Column;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\Filter;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\HtmlString;
use Rmsramos\Activitylog\Resources\ActivitylogResource as RmsramosActivityLogResource;
use Illuminate\Support\Str;

class ActivityLogResource extends RmsramosActivityLogResource
{
    protected static ?string $navigationGroup = 'Administrador';
    protected static ?int $navigationSort = 2;

    private static function getCauserName(Model $record)
    {
        if ($record->causer_id == null) {
            return new HtmlString('&mdash;');
        }

        if ($record->causer_type === \App\Models\User::class) {
            return $record->causer->name;
        }

        if ($record->causer_type === \App\Models\InscricaoPessoa::class) {
            return $record->causer->nome;
        }
    }

    public static function getCauserNameColumnCompoment(): Column
    {
        return TextColumn::make('causer.name')
            ->label(__('activitylog::tables.columns.causer.label'))
            ->getStateUsing(function (Model $record) {

                if ($record->causer_id == null) {
                    return new HtmlString('&mdash;');
                }

                return $record->causer->name;
            });
    }

    public static function getCandidateCauserNameColumnCompoment(): Column
    {
        return TextColumn::make('causer.nome')
            ->label('Candidato');
    }

    public static function getUserFilterComponent(): SelectFilter
    {
        return SelectFilter::make('causer_id')
            ->label('Usuário')
            ->preload()
            ->searchable()
            ->options(fn() => User::all()->pluck('name', 'id'));
    }

    public static function table(Table $table): Table
    {
        return $table
            ->heading('Logs de Atividade do Sistema')
            ->description('Visualize e audite ações de usuários no sistema. Use o filtro lateral para localizar registros conforme critérios definidos.')
            ->columns([
                // static::getLogNameColumnCompoment(),
                static::getEventColumnCompoment(),
                static::getSubjectTypeColumnCompoment(),
                static::getCauserNameColumnCompoment(),
                // static::getPropertiesColumnCompoment(),
                static::getCreatedAtColumnCompoment(),
            ])
            ->defaultSort(config('filament-activitylog.resources.default_sort_column', 'created_at'), config('filament-activitylog.resources.default_sort_direction', 'asc'))
            ->filters([
                static::getDateFilterComponent(),
                static::getEventFilterCompoment(),
                static::getUserFilterComponent(),
            ])
            ->headerActions([
                ExportAction::make()
                    ->label('Exportar Logs')
                    ->icon('heroicon-o-arrow-down-tray')
                    ->color('primary')
                    ->exporter(ActivityExporter::class)
            ]);
    }

    protected static function formatDateValues($values)
    {
        // Assuming the formatDateValues function formats date values in some way
        return collect($values)->map(function ($value) {
            // Example: format date values
            if ($value instanceof \DateTimeInterface) {
                return $value->format('Y-m-d H:i:s');
            }
            return $value;
        });
    }

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Split::make([
                    Section::make([
                        TextInput::make('causer_id')
                            ->afterStateHydrated(function ($component, ?Model $record) {
                                /** @phpstan-ignore-next-line */
                                return $component->state(static::getCauserName($record));
                            })
                            ->label(__('activitylog::forms.fields.causer.label')),

                        TextInput::make('subject_type')
                            ->afterStateHydrated(function ($component, ?Model $record, $state) {
                                /** @var Activity&ActivityModel $record */
                                return $state ? $component->state(Str::of($state)->afterLast('\\')->headline() . ' # ' . $record->subject_id) : '-';
                            })
                            ->label(__('activitylog::forms.fields.subject_type.label')),

                        Textarea::make('description')
                            ->label(__('activitylog::forms.fields.description.label'))
                            ->rows(2)
                            ->columnSpan('full'),
                    ]),
                    Section::make([
                        Placeholder::make('log_name')
                            ->content(function (?Model $record): string {
                                /** @var Activity&ActivityModel $record */
                                return $record->log_name ? ucwords($record->log_name) : '-';
                            })
                            ->label(__('activitylog::forms.fields.log_name.label')),

                        Placeholder::make('event')
                            ->content(function (?Model $record): string {
                                /** @phpstan-ignore-next-line */
                                return $record?->event ? ucwords($record?->event) : '-';
                            })
                            ->label(__('activitylog::forms.fields.event.label')),

                        Placeholder::make('created_at')
                            // ->label(__('activitylog::forms.fields.created_at.label'))
                            ->label('Criado em')
                            ->content(function (?Model $record): string {
                                /** @var Activity&ActivityModel $record */
                                return $record->created_at ? "{$record->created_at->format(config('filament-activitylog.datetime_format', 'd/m/Y H:i:s'))}" : '-';
                            }),
                    ])->grow(false),
                ])->from('md'),

                Section::make()
                    ->columns()
                    ->visible(fn($record) => $record->properties?->count() > 0)
                    ->schema(function (?Model $record) {
                        /** @var Activity&ActivityModel $record */
                        $properties = $record->properties->except(['attributes', 'old']);

                        $schema = [];

                        if ($properties->count()) {
                            $schema[] = KeyValue::make('properties')
                                ->label(__('activitylog::forms.fields.properties.label'))
                                ->columnSpan('full');
                        }

                        if ($old = $record->properties->get('old')) {
                            $schema[] = KeyValue::make('old')
                                ->formatStateUsing(fn() => self::formatDateValues($old))
                                ->label(__('activitylog::forms.fields.old.label'));
                        }

                        if ($attributes = $record->properties->get('attributes')) {
                            $schema[] = KeyValue::make('attributes')
                                ->formatStateUsing(fn() => self::formatDateValues($attributes))
                                ->label(__('activitylog::forms.fields.attributes.label'));
                        }

                        return $schema;
                    }),
            ])->columns(1);
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListActivityLogs::route('/'),
            'view' => Pages\ViewActivityLog::route('/{record}'),
        ];
    }
}

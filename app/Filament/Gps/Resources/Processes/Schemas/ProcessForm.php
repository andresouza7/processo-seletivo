<?php

namespace App\Filament\Gps\Resources\Processes\Schemas;

use Filament\Forms\Components\Checkbox;
use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\Repeater;
use Filament\Forms\Components\RichEditor;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Schemas\Components\Fieldset;
use Filament\Schemas\Components\Group;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Components\Utilities\Get;
use Filament\Schemas\Schema;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Validation\Rules\Unique;

class ProcessForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Section::make([
                    Group::make([
                        TextInput::make('title')
                            ->label('Título')
                            ->columnSpan(2)
                            ->required(),
                        Select::make('is_published')
                            ->label('Status')
                            ->required()
                            ->options([
                                true => 'Publicado',
                                false => 'Despublicado'
                            ]),

                    ])->columns(3),
                    Group::make([
                        Select::make('process_type_id')
                            ->label('Tipo')
                            ->columnSpan(2)
                            ->relationship('type', 'description')
                            ->disabledOn('edit')
                            ->required(),
                        TextInput::make('number')
                            ->label('Número')
                            ->disabledOn('edit')
                            ->mask('999/9999')
                            ->placeholder('Ex: 001/2025')
                            ->unique(
                                table: 'processes',
                                column: 'number',
                                modifyRuleUsing: fn(Unique $rule, Get $get) => $rule->where('process_type_id', $get('process_type_id') ?? null)
                            )
                            ->required(),
                    ])->columns(3),

                    RichEditor::make('description')
                        ->disableToolbarButtons(['attachFiles'])
                        ->required()
                        ->label('Descrição'),

                    Select::make('roles')
                        ->label('Administradores do PS')
                        ->relationship('roles', 'name', modifyQueryUsing: fn(Builder $query) => $query->whereNotIn('name', ['admin', 'avaliador']))
                        ->multiple()
                        ->searchable()
                        ->preload()
                        ->required(),

                    Group::make([
                        Fieldset::make('Período de Publicação')
                            ->schema([
                                DatePicker::make('publication_start_date')
                                    ->label('Início')
                                    ->required(),
                                DatePicker::make('publication_end_date')
                                    ->label('Fim')
                                    ->required()
                            ])->columnSpan(1),
                        Fieldset::make('Período de Inscrições')
                            ->schema([
                                DatePicker::make('application_start_date')
                                    ->label('Início')
                                    ->required(),
                                DatePicker::make('application_end_date')
                                    ->label('Fim')
                                    ->required(),
                            ])->columnSpan(1),
                    ])->columns(2),

                    Checkbox::make('has_fee')
                        ->label('Permitir isenção da taxa de inscrição'),

                    Checkbox::make('multiple_applications')
                        ->label('Permitir inscrição em mais de uma vaga'),

                    Repeater::make('attachment_fields')
                        ->label('Documentos Requeridos')
                        ->schema([
                            TextInput::make('item')
                                ->label('Nome do Documento')
                                ->maxLength(100)
                                ->required(), //causou problemas com testes (factory), mas deixa como está
                            Textarea::make('description')
                                ->label('Descrição')
                                ->maxLength(250)
                                ->required()
                        ])
                        ->itemLabel(fn(array $state): ?string => $state['item'] ?? null)
                        ->cloneable()
                        ->compact()
                        ->collapsed()
                        ->columnSpanFull()
                        // ->minItems(1)
                        ->maxItems(15)
                        ->addActionLabel('Adicionar Documento')
                        ->defaultItems(function ($record) {
                            return $record->attachment_fields ?? [];
                        }),
                ])
            ])->columns(2);
    }
}

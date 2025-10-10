<?php

namespace App\Filament\Gps\Resources\ProcessoSeletivos\Schemas;

use Filament\Forms\Components\Checkbox;
use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\Repeater;
use Filament\Forms\Components\RichEditor;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Schemas\Components\Fieldset;
use Filament\Schemas\Components\Group;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;

class ProcessoSeletivoForm
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
                        Select::make('process_type_id')
                            ->label('Tipo')
                            ->relationship('type', 'description')
                            ->required(),
                    ])->columns(3),

                    Group::make([
                        TextInput::make('number')
                            ->disabledOn('edit')
                            ->placeholder('Ex: 01/2025')
                            ->required(),
                        DatePicker::make('document_date')
                            ->label('Data do Edital')
                            ->required(),
                        Select::make('is_published')
                            ->required()
                            ->options([
                                true => 'Sim',
                                false => 'Não'
                            ]),
                    ])->columns(3),

                    RichEditor::make('description')
                        ->required()
                        ->label('Descrição'),

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

                    Checkbox::make('has_fee_exemption')
                        ->label('Possui isenção da taxa de inscrição'),

                    Repeater::make('attachment_fields')
                        ->label('Documentos Requeridos')
                        ->schema([
                            TextInput::make('item')
                                ->label('Nome do Documento')
                                ->required() //causou problemas com testes (factory), mas deixa como está
                        ])
                        ->itemLabel(fn (array $state): ?string => $state['item'] ?? null)
                        ->cloneable()
                        ->compact()
                        ->collapsed()
                        ->columnSpanFull()
                        // ->minItems(1)
                        ->addActionLabel('Adicionar Documento')
                        ->defaultItems(function ($record) {
                            return $record->attachment_fields ?? [];
                        }),
                ])
            ])->columns(2);
    }
}

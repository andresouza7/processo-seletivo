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
                                ->required() //causou problemas com testes (factory), mas deixa como está
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
}

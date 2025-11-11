<?php

namespace App\Filament\Gps\Resources\Processes\Schemas;

use App\Models\Quota;
use Filament\Forms\Components\Checkbox;
use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\Hidden;
use Filament\Forms\Components\KeyValue;
use Filament\Forms\Components\Repeater;
use Filament\Forms\Components\Repeater\TableColumn;
use Filament\Forms\Components\RichEditor;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Schemas\Components\Fieldset;
use Filament\Schemas\Components\Group;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Components\Utilities\Get;
use Filament\Schemas\Schema;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Validation\Rules\Unique;
use Illuminate\Support\Str;

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

                    Group::make([
                        Fieldset::make('Período de Inscrições')
                            ->schema([
                                DatePicker::make('application_start_date')
                                    ->label('Início')
                                    ->required(),
                                DatePicker::make('application_end_date')
                                    ->label('Fim')
                                    ->required(),
                            ])->columnSpan(1),
                        Fieldset::make('Período de Publicação')
                            ->schema([
                                DatePicker::make('publication_start_date')
                                    ->label('Início')
                                    ->required(),
                                DatePicker::make('publication_end_date')
                                    ->label('Fim')
                                    ->required()
                            ])->columnSpan(1),
                    ])->columns(2),

                    Checkbox::make('has_fee')
                        ->label('Ofertar isenção da taxa de inscrição')
                        ->helperText('Para certames com taxa de inscrição. Será disponibilizado campo para upload do comprovante.'),
                        
                    Checkbox::make('has_assistance')
                        ->label('Ofertar atendimento especial')
                        ->helperText('Para certames com aplicação de prova. Será disponibilizado campo para especificação do atendimento.'),

                    Select::make('roles')
                        ->label('Compartilhar com:')
                       ->helperText('Apenas o autor e usuários com o mesmo perfil podem gerenciar o processo seletivo. Compartilhe para extender esse privilégio a outros perfis.')
                        ->relationship('roles', 'name', modifyQueryUsing: fn(Builder $query) => $query->whereNotIn('name', ['admin', 'avaliador']))
                        ->multiple()
                        ->searchable()
                        ->preload()
                        ->required(),
                ])
                    ->heading('Dados do Processo'),

                Section::make([
                    Repeater::make('position')
                        ->relationship()
                        ->hiddenLabel()
                        ->table([
                            TableColumn::make('Descrição')->alignStart()->markAsRequired(),
                            TableColumn::make('Código')->alignStart()->width('180px')
                        ])
                        ->schema([
                            TextInput::make('description')->label('Descrição')->required(),
                            TextInput::make('code')->label('Código'),
                        ])
                        ->deletable(false)
                        ->addActionLabel('Adicionar vaga')
                        ->minItems(1)
                        ->compact()
                        ->columnSpanFull(),

                    Checkbox::make('multiple_applications')
                        ->label('Permitir inscrição em mais de uma vaga')
                        ->helperText('Aceitará a última inscrição por vaga'),

                    Select::make('quota_id')
                        ->label('Disponibilizar Cotas')
                        ->helperText('O edital aplica políticas de inclusão')
                        ->placeholder('Selecione as opções')
                        ->relationship('quotas', 'description')
                        ->multiple()
                        ->preload()
                        ->searchable()
                ])
                    ->heading('Gerenciar Vagas')
                    ->description('Vagas ofertadas no edital. Por padrão, apenas a última inscrição do candidato é validada, independente da vaga.'),

                Section::make([
                    Repeater::make('formFields')
                        // ->label('Campos do Formulário')
                        ->hiddenLabel()
                        ->relationship() // usa o hasMany 'formFields'
                        ->orderColumn('order')
                        ->reorderableWithDragAndDrop()
                        ->schema([
                            TextInput::make('label')
                                ->label('Nome do campo')
                                ->required()
                                ->maxLength(255),

                            // TextInput::make('name')
                            //     ->label('Nome do campo (slug)')
                            //     ->helperText('Use letras minúsculas e underscore, ex: cpf, data_nascimento')
                            //     ->required()
                            //     ->maxLength(255),

                            Select::make('type')
                                ->label('Tipo do campo')
                                ->required()
                                ->options([
                                    'text' => 'Texto',
                                    'textarea' => 'Área de texto',
                                    'number' => 'Número',
                                    'email' => 'E-mail',
                                    'date' => 'Data',
                                    'select' => 'Seleção (Select)',
                                    'checkbox' => 'Caixa de seleção (Checkbox)',
                                    // 'file' => 'Upload de arquivo',
                                ])
                                ->reactive(),

                            Toggle::make('required')
                                ->label('Obrigatório')
                                ->default(false)
                                ->columnSpanFull(),

                            Repeater::make('options')
                                ->label('Opções (para selects)')
                                ->table([
                                    TableColumn::make('Opção')->alignStart()
                                ])
                                ->schema([
                                    TextInput::make('label')
                                        ->label('Rótulo')
                                        ->afterStateUpdated(function (callable $set, $state) {
                                            $set('value', Str::slug($state));
                                        }),
                                    Hidden::make('value'),
                                ])
                                ->visible(fn(callable $get) => $get('type') === 'select')
                                ->helperText('Adicione as opções disponíveis para o campo select.')
                                ->compact()
                                ->columns(1)
                                ->columnSpanFull(),

                            // KeyValue::make('options')
                            //     ->label('Opções (para selects)')
                            //     ->keyLabel('Valor')
                            //     ->editableKeys(false)
                            //     ->editableValues(false)
                            //     ->valueLabel('Rótulo')
                            //     ->visible(fn(callable $get) => $get('type') === 'select')
                            //     ->helperText('Adicione as opções disponíveis para o campo select.')
                            //     ->columnSpanFull(),

                            Textarea::make('helper_text')
                                ->label('Texto de ajuda (opcional)')
                                ->columnSpanFull()
                                ->visible(fn(callable $get) => $get('type') !== 'checkbox')
                                ->placeholder('Ex: Informe seu CPF sem pontos e traços'),
                        ])
                        ->itemLabel(fn(array $state): ?string => $state['label'] ?? null)
                        ->columns(2)
                        ->collapsible()
                        ->collapsed()
                        ->defaultItems(0)
                        ->mutateRelationshipDataBeforeCreateUsing(function ($data) {
                            $data['name'] = Str::slug($data['label']);

                            return $data;
                        })
                        ->addActionLabel('Adicionar novo campo'),
                ])
                    ->heading('Campos do Formulário')
                    ->description('Personalize o formulário de inscrição com perguntas e respostas customizadas.'),

                Section::make([
                    Repeater::make('attachment_fields')
                        // ->label('Documentos Anexos')
                        ->hiddenLabel()
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
                        ->addActionLabel('Adicionar documento')
                        ->defaultItems(function ($record) {
                            return $record->attachment_fields ?? [];
                        }),
                ])
                    ->heading('Anexos do Candidato')
                    ->description('Exigir o envio de documentos em formato PDF no momento da inscrição.')


            ])->columns(2);
    }
}

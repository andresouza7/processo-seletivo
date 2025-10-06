<?php

namespace App\Filament\Gps\Resources\InscricaoPessoas\Schemas;

use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Schemas\Components\Fieldset;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;

class InscricaoPessoaForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Section::make('Dados Cadastrais')
                    ->columns(2)
                    ->schema([
                        TextInput::make('nome')->required(),
                        TextInput::make('mae')->label('Nome da Mãe')->required(),
                        TextInput::make('cpf')
                            ->label('CPF')
                            ->required(),
                        TextInput::make('ci')
                            ->label('RG')
                            ->required(),
                        DatePicker::make('data_nascimento')
                            ->label('Data de Nascimento')
                            ->minDate('1950-01-01')
                            ->required(),
                        Select::make('sexo')
                            ->label('Sexo')
                            ->options([
                                'M' => 'Masculino',
                                'F' => 'Feminino'
                            ])
                            ->required(),
                        TextInput::make('nome_social'),
                        TextInput::make('identidade_genero')
                            ->label('Identidade de Gênero'),

                        TextInput::make('telefone')->label('Telefone')->required()->columnSpanFull(),
                        Fieldset::make('Endereço')
                            ->extraAttributes((['class' => 'mt-4']))
                            ->schema([
                                TextInput::make('endereco')->label('Logradouro')->required(),
                                TextInput::make('bairro')->label('Bairro')->required(),
                                TextInput::make('numero')->label('Número')->required(),
                                TextInput::make('complemento')->label('Complemento'),
                                TextInput::make('cidade')->label('Cidade')->required(),
                            ])
                    ]),
            ]);
    }
}

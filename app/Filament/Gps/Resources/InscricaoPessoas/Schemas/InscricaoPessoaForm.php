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
                        TextInput::make('name')->required(),
                        TextInput::make('mother_name')->label('Nome da Mãe')->required(),
                        TextInput::make('cpf')
                            ->label('CPF')
                            ->required(),
                        TextInput::make('rg')
                            ->label('RG')
                            ->required(),
                        DatePicker::make('birth_date')
                            ->label('Data de Nascimento')
                            ->minDate('1950-01-01')
                            ->required(),
                        Select::make('sex')
                            ->label('Sexo')
                            ->options([
                                'M' => 'Masculino',
                                'F' => 'Feminino'
                            ])
                            ->required(),
                        TextInput::make('social_name'),
                        TextInput::make('gender_identity')
                            ->label('Identidade de Gênero'),

                        TextInput::make('phone')->label('Telefone')->required()->columnSpanFull(),
                        Fieldset::make('Endereço')
                            ->extraAttributes((['class' => 'mt-4']))
                            ->schema([
                                TextInput::make('address')->label('Logradouro')->required(),
                                TextInput::make('district')->label('Bairro')->required(),
                                TextInput::make('address_number')->label('Número')->required(),
                                TextInput::make('address_complement')->label('Complemento'),
                                TextInput::make('city')->label('Cidade')->required(),
                            ])
                    ]),
            ]);
    }
}

<?php

namespace App\Filament\Gps\Resources\InscricaoPessoas\Schemas;

use Filament\Infolists\Components\TextEntry;
use Filament\Schemas\Components\Fieldset;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;

class InscricaoPessoaInfolist
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Section::make('Dados Cadastrais')
                    ->columns(2)
                    ->schema([
                        TextEntry::make('nome')->label('Nome'),
                        TextEntry::make('mae')->label('Nome da Mãe'),
                        TextEntry::make('cpf')->label('CPF'),
                        TextEntry::make('ci')->label('RG'),
                        TextEntry::make('data_nascimento')->label('Data de Nascimento'),
                        TextEntry::make('sexo')
                            ->label('Sexo')
                            ->formatStateUsing(fn($state) => [
                                'M' => 'Masculino',
                                'F' => 'Feminino',
                            ][$state] ?? $state),
                        TextEntry::make('nome_social')->label('Nome Social'),
                        TextEntry::make('identidade_genero')->label('Identidade de Gênero'),
                        TextEntry::make('telefone')->label('Telefone'),
                        TextEntry::make('email')->label('Email'),

                        Fieldset::make('Endereço')
                            ->extraAttributes(['class' => 'mt-4'])
                            ->schema([
                                TextEntry::make('cep')->label('CEP'),
                                TextEntry::make('endereco')->label('Logradouro'),
                                TextEntry::make('bairro')->label('Bairro'),
                                TextEntry::make('numero')->label('Número'),
                                TextEntry::make('complemento')->label('Complemento'),
                                TextEntry::make('cidade')->label('Cidade'),
                            ]),
                    ]),
            ]);
    }
}

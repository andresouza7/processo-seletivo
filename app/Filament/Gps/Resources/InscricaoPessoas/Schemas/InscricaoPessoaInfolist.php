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
                        TextEntry::make('name')->label('Nome'),
                        TextEntry::make('mother_name')->label('Nome da Mãe'),
                        TextEntry::make('cpf')->label('CPF'),
                        TextEntry::make('rg')->label('RG'),
                        TextEntry::make('birth_date')->label('Data de Nascimento')->date('d/m/Y'),
                        TextEntry::make('sex')
                            ->label('Sexo')
                            ->formatStateUsing(fn($state) => [
                                'M' => 'Masculino',
                                'F' => 'Feminino',
                            ][$state] ?? $state),
                        TextEntry::make('social_name')->label('Nome Social'),
                        TextEntry::make('gender_identity')->label('Identidade de Gênero'),
                        TextEntry::make('phone')->label('Telefone'),
                        TextEntry::make('email')->label('Email'),

                        Fieldset::make('Endereço')
                            ->extraAttributes(['class' => 'mt-4'])
                            ->schema([
                                TextEntry::make('postal_code')->label('CEP'),
                                TextEntry::make('address')->label('Logradouro'),
                                TextEntry::make('district')->label('Bairro'),
                                TextEntry::make('address_number')->label('Número'),
                                TextEntry::make('address_complement')->label('Complemento'),
                                TextEntry::make('city')->label('Cidade'),
                            ]),
                    ]),
            ]);
    }
}

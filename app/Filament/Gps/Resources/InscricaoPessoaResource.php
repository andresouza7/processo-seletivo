<?php

namespace App\Filament\Gps\Resources;

use App\Filament\Gps\Resources\InscricaoPessoaResource\Pages;
use App\Filament\Resources\InscricaoPessoaResource\RelationManagers;
use App\Models\InscricaoPessoa;
use Filament\Forms;
use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\Fieldset;
use Filament\Forms\Components\Section;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Wizard;
use Filament\Forms\Components\Wizard\Step;
use Filament\Forms\Form;
use Filament\Infolists\Components\TextEntry;
use Filament\Infolists\Infolist;
use Filament\Notifications\Notification;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Str;

class InscricaoPessoaResource extends Resource
{
    protected static ?string $model = InscricaoPessoa::class;
    protected static ?string $modelLabel = 'Candidato';
    protected static ?string $slug = 'candidatos';
    protected static ?string $navigationIcon = 'heroicon-o-users';
    protected static ?string $navigationGroup = 'Gerenciar';
    protected static ?int $navigationSort = 2;
    protected static ?string $recordTitleAttribute = 'nome';

    public static function canAccess(): bool
    {
        return Auth::user()->hasRole('gestor|admin');
    }

    public static function infolist(Infolist $infolist): Infolist
    {
        return $infolist
            ->schema([
                \Filament\Infolists\Components\Section::make('Dados Cadastrais')
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

                        \Filament\Infolists\Components\Fieldset::make('Endereço')
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

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
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

    public static function table(Table $table): Table
    {
        return $table
            ->heading('Contas de candidatos')
            ->description('Gerência dos dados cadastrais dos dandidatos e redefinição de senha.')
            ->defaultSort('nome')
            ->columns([
                Tables\Columns\TextColumn::make('nome')
                    ->searchable(),
                Tables\Columns\TextColumn::make('sexo')
                    ->badge()
                    ->searchable(),
                Tables\Columns\TextColumn::make('ci')
                    ->label('RG')
                    ->searchable(),
                Tables\Columns\TextColumn::make('cpf')
                    ->label('CPF')
                    ->searchable(),
                Tables\Columns\TextColumn::make('email')
                    ->searchable(),
            ])
            ->filters([
                //
            ])
            ->actions([
                Tables\Actions\ViewAction::make(),

            ]);
    }

    public static function getRelations(): array
    {
        return [
            //
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListInscricaoPessoas::route('/'),
            // 'edit' => Pages\EditInscricaoPessoa::route('/{record}/edit'),
            'view' => Pages\ViewInscricaoPessoa::route('/{record}/view'),
        ];
    }
}

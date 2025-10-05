<?php

namespace App\Filament\Gps\Resources;

use Filament\Schemas\Schema;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Components\Fieldset;
use Filament\Tables\Columns\TextColumn;
use Filament\Actions\ViewAction;
use App\Filament\Gps\Resources\InscricaoPessoaResource\Pages\ListInscricaoPessoas;
use App\Filament\Gps\Resources\InscricaoPessoaResource\Pages\ViewInscricaoPessoa;
use App\Filament\Gps\Resources\InscricaoPessoaResource\Pages;
use App\Filament\Resources\InscricaoPessoaResource\RelationManagers;
use App\Models\InscricaoPessoa;
use Filament\Forms;
use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Wizard;
use Filament\Forms\Components\Wizard\Step;
use Filament\Infolists\Components\TextEntry;
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
    protected static string | \BackedEnum | null $navigationIcon = 'heroicon-o-users';
    protected static string | \UnitEnum | null $navigationGroup = 'Gerenciar';
    protected static ?int $navigationSort = 2;
    protected static ?string $recordTitleAttribute = 'nome';

    public static function canAccess(): bool
    {
        return Auth::user()->hasRole('gestor|admin');
    }

    public static function infolist(Schema $schema): Schema
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

    public static function form(Schema $schema): Schema
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

    public static function table(Table $table): Table
    {
        return $table
            ->heading('Contas de candidatos')
            ->description('Gerência dos dados cadastrais dos dandidatos e redefinição de senha.')
            ->defaultSort('nome')
            ->columns([
                TextColumn::make('nome')
                    ->searchable(),
                TextColumn::make('sexo')
                    ->badge()
                    ->searchable(),
                TextColumn::make('ci')
                    ->label('RG')
                    ->searchable(),
                TextColumn::make('cpf')
                    ->label('CPF')
                    ->searchable(),
                TextColumn::make('email')
                    ->searchable(),
            ])
            ->filters([
                //
            ])
            ->recordActions([
                ViewAction::make(),

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
            'index' => ListInscricaoPessoas::route('/'),
            // 'edit' => Pages\EditInscricaoPessoa::route('/{record}/edit'),
            'view' => ViewInscricaoPessoa::route('/{record}/view'),
        ];
    }
}

<?php

namespace App\Filament\Gps\Resources;

use App\Filament\Gps\Resources\InscricaoResource\Pages;
use App\Models\Inscricao;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Infolists\Components\Section;
use Filament\Infolists\Components\TextEntry;
use Filament\Infolists\Infolist;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;

class InscricaoResource extends Resource
{
    protected static ?string $model = Inscricao::class;
    protected static ?string $modelLabel = 'Inscrição';
    protected static ?string $pluralModelLabel = 'Inscrições';
    protected static ?string $slug = 'inscricoes';
    protected static ?string $navigationIcon = 'heroicon-o-table-cells';
    protected static ?string $navigationGroup = 'Gerenciar';
    protected static ?int $navigationSort = 3;

    protected static bool $shouldRegisterNavigation = false;

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\TextInput::make('cod_inscricao')
                    ->disabled(),
                Forms\Components\Textarea::make('local_prova')
                    ->columnSpanFull(),
                Forms\Components\TextInput::make('ano_enem')
                    ->maxLength(4),
                Forms\Components\TextInput::make('bonificacao')
                    ->maxLength(1),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->heading('Consultar Inscrições')
            ->description('Informações sobre as inscrições realizadas nos processos seletivos da UEAP. Consulte ou edite um registro de inscrição.')
            ->defaultSort('data_hora', 'desc')
            ->columns([
                Tables\Columns\TextColumn::make('cod_inscricao')
                    ->searchable(),
                Tables\Columns\TextColumn::make('inscricao_pessoa.nome')
                    ->label('Nome Candidato')
                    ->searchable(),
                Tables\Columns\TextColumn::make('processo_seletivo.titulo')
                    ->limit(30)
                    ->description(fn($record) => substr($record->inscricao_vaga->descricao, 0, 28))
                    ->sortable(),
                Tables\Columns\TextColumn::make('tipo_vaga.descricao')
                    ->sortable(),
                Tables\Columns\TextColumn::make('data_hora')
                    ->date()
                    ->sortable(),
            ])
            ->filters([
                //
            ])
            ->actions([
                Tables\Actions\ViewAction::make(),
                Tables\Actions\EditAction::make(),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    // Tables\Actions\DeleteBulkAction::make(),
                ]),
            ]);
    }

    public static function infolist(Infolist $infolist): Infolist
    {
        return $infolist
            ->schema([
                Section::make('Detalhes da Inscrição')
                    ->columns(2)
                    ->schema([
                        TextEntry::make('cod_inscricao')
                            ->label('Cód. Inscrição'),
                        TextEntry::make('processo_seletivo.titulo')
                            ->label('Processo Seletivo'),
                        TextEntry::make('inscricao_vaga.descricao')
                            ->label('Vaga'),
                        TextEntry::make('inscricao_pessoa.nome')
                            ->label('Candidato'),
                        TextEntry::make('tipo_vaga.descricao')
                            ->label('Tipo Vaga'),
                        TextEntry::make('data_hora')
                            ->label('Data')
                            ->date(),
                        TextEntry::make('necessita_atendimento')
                            ->badge()
                            ->label('Necessita Atendimento'),
                        TextEntry::make('qual_atendimento')
                            ->label('Qual Atendimento'),
                        TextEntry::make('observacao')
                            ->label('Observação'),
                        TextEntry::make('local_prova')
                            ->label('Local da Prova'),
                        TextEntry::make('ano_enem')
                            ->label('Ano ENEM'),
                        TextEntry::make('bonificacao')
                            ->badge()
                            ->label('Bonificação'),
                    ]),
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
            'index' => Pages\ListInscricaos::route('/'),
            'create' => Pages\CreateInscricao::route('/create'),
            'view' => Pages\ViewInscricao::route('/{record}'),
            'edit' => Pages\EditInscricao::route('/{record}/edit'),
        ];
    }
}

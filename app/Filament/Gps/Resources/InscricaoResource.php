<?php

namespace App\Filament\Gps\Resources;

use Filament\Schemas\Schema;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Textarea;
use Filament\Tables\Columns\TextColumn;
use Filament\Actions\ViewAction;
use Filament\Actions\EditAction;
use Filament\Actions\BulkActionGroup;
use Filament\Schemas\Components\Section;
use App\Filament\Gps\Resources\InscricaoResource\Pages\ListInscricaos;
use App\Filament\Gps\Resources\InscricaoResource\Pages\CreateInscricao;
use App\Filament\Gps\Resources\InscricaoResource\Pages\ViewInscricao;
use App\Filament\Gps\Resources\InscricaoResource\Pages\EditInscricao;
use App\Filament\Gps\Resources\InscricaoResource\Pages;
use App\Models\Inscricao;
use Filament\Forms;
use Filament\Infolists\Components\TextEntry;
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
    protected static string | \BackedEnum | null $navigationIcon = 'heroicon-o-table-cells';
    protected static string | \UnitEnum | null $navigationGroup = 'Gerenciar';
    protected static ?int $navigationSort = 3;

    protected static bool $shouldRegisterNavigation = false;

    public static function form(Schema $schema): Schema
    {
        return $schema
            ->components([
                TextInput::make('cod_inscricao')
                    ->disabled(),
                Textarea::make('local_prova')
                    ->columnSpanFull(),
                TextInput::make('ano_enem')
                    ->maxLength(4),
                TextInput::make('bonificacao')
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
                TextColumn::make('cod_inscricao')
                    ->searchable(),
                TextColumn::make('inscricao_pessoa.nome')
                    ->label('Nome Candidato')
                    ->searchable(),
                TextColumn::make('processo_seletivo.titulo')
                    ->limit(30)
                    ->description(fn($record) => substr($record->inscricao_vaga->descricao, 0, 28))
                    ->sortable(),
                TextColumn::make('tipo_vaga.descricao')
                    ->sortable(),
                TextColumn::make('data_hora')
                    ->date()
                    ->sortable(),
            ])
            ->filters([
                //
            ])
            ->recordActions([
                ViewAction::make(),
                EditAction::make(),
            ])
            ->toolbarActions([
                BulkActionGroup::make([
                    // Tables\Actions\DeleteBulkAction::make(),
                ]),
            ]);
    }

    public static function infolist(Schema $schema): Schema
    {
        return $schema
            ->components([
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
            'index' => ListInscricaos::route('/'),
            'create' => CreateInscricao::route('/create'),
            'view' => ViewInscricao::route('/{record}'),
            'edit' => EditInscricao::route('/{record}/edit'),
        ];
    }
}

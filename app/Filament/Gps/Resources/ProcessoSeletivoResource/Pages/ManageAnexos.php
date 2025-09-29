<?php

namespace App\Filament\Gps\Resources\ProcessoSeletivoResource\Pages;

use App\Filament\Gps\Resources\ProcessoSeletivoResource;
use App\Models\ProcessoSeletivoAnexo;
use Filament\Actions;
use Filament\Forms;
use Filament\Forms\Components\SpatieMediaLibraryFileUpload;
use Filament\Forms\Form;
use Filament\Resources\Pages\ManageRelatedRecords;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Carbon;

class ManageAnexos extends ManageRelatedRecords
{
    protected static string $resource = ProcessoSeletivoResource::class;
    protected static ?string $title = 'Gerenciar Anexos';
    protected static string $relationship = 'anexos';
    protected static ?string $navigationIcon = 'heroicon-o-paper-clip';

    public static function getNavigationLabel(): string
    {
        return 'Anexos';
    }

    public function form(Form $form): Form
    {
        return $form
            ->columns(1)
            ->schema([
                Forms\Components\TextInput::make('descricao')
                    ->required()
                    ->maxLength(255),
                SpatieMediaLibraryFileUpload::make('arquivo')
                    ->label('Arquivo')
                    ->acceptedFileTypes(['application/pdf'])
                    ->helperText('* É necessário salvar as alterações após a inclusão do arquivo.')
            ]);
    }

    public function table(Table $table): Table
    {
        return $table
            ->recordTitleAttribute('descricao')
            ->heading('Anexos')
            ->columns([
                Tables\Columns\TextColumn::make('idprocesso_seletivo_anexo')->searchable()->limit(70),
                Tables\Columns\TextColumn::make('descricao')->searchable()->limit(70),
            ])
            ->headerActions([
                Tables\Actions\CreateAction::make()
                    ->label('Novo Anexo')
                    ->createAnother(false)
                    ->databaseTransaction()
                    ->mutateFormDataUsing(function (array $data) {

                        $id = ProcessoSeletivoAnexo::latest('idprocesso_seletivo_anexo')
                            ->value('idprocesso_seletivo_anexo') ?? 0;
                        $data['idprocesso_seletivo_anexo'] = $id + 1;
                        $data['data_publicacao'] = now();
                        $data['acessos'] = 0;

                        return $data;
                    }),
            ])
            ->actions([
                Tables\Actions\EditAction::make()
                    ->hidden(fn($record) => Carbon::parse($record->data_publicacao)->lt(Carbon::parse('2024-11-01'))),
                Tables\Actions\DeleteAction::make(),
                Tables\Actions\Action::make('download')
                    // ->disabled(fn($record) => !$record->url_arquivo)
                    ->label('Download')
                    ->icon('heroicon-o-arrow-down-tray')
                    // ->url(fn($record) => dd('anexo.show', $record->getFirstMedia('documentos_requeridos')))
                    ->openUrlInNewTab(),
            ]);
    }
}

<?php

namespace App\Filament\Gps\Resources\ProcessoSeletivoResource\RelationManagers;

use Carbon\Carbon;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\RelationManagers\RelationManager;
use Filament\Tables;
use Filament\Tables\Table;
use Filament\Forms\Components\SpatieMediaLibraryFileUpload;
use Illuminate\Database\Eloquent\Model;
use Livewire\Features\SupportFileUploads\TemporaryUploadedFile;

class AnexosRelationManager extends RelationManager
{
    protected static ?string $modelLabel = 'Anexo';
    protected static string $relationship = 'anexos';

    public $arquivo;

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
                    ->helperText('* É necessário salvar as alterações após a inclusão do arquivo.')
            ]);
    }

    public function table(Table $table): Table
    {
        return $table
            ->recordTitleAttribute('descricao')
            ->defaultSort('idprocesso_seletivo_anexo', 'desc')
            ->columns([
                Tables\Columns\TextColumn::make('descricao'),
                Tables\Columns\TextColumn::make('data_publicacao')
                    ->date('d/m/Y'),
            ])
            ->filters([
                //
            ])
            ->headerActions([
                Tables\Actions\CreateAction::make()
                    ->databaseTransaction()
                    ->mutateFormDataUsing(function (array $data) {

                        // $data['idarquivo'] = $this->arquivo->idarquivo;
                        $data['data_publicacao'] = now();
                        $data['acessos'] = 0;

                        return $data;
                    })
            ])
            ->actions([
                Tables\Actions\EditAction::make()
                    ->hidden(fn($record) => Carbon::parse($record->data_publicacao)->lt(Carbon::parse('2024-11-01'))),
                Tables\Actions\DeleteAction::make(),
                Tables\Actions\Action::make('download')
                    ->disabled(fn($record) => !$record->url_arquivo)
                    ->label('Download')
                    ->icon('heroicon-o-arrow-down-tray')
                    ->url(fn($record) => $record->url_arquivo)
                    ->openUrlInNewTab(),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    // Tables\Actions\DeleteBulkAction::make(),
                ]),
            ]);
    }
}

<?php

namespace App\Filament\Exports;

use App\Models\Inscricao;
use Filament\Actions\Exports\ExportColumn;
use Filament\Actions\Exports\Exporter;
use Filament\Actions\Exports\Models\Export;

class InscricaoExporter extends Exporter
{
    protected static ?string $model = Inscricao::class;

    public static function getColumns(): array
    {
        return [
            // Dados Inscrição
            ExportColumn::make('cod_inscricao')->label('Cod Inscrição'),
            ExportColumn::make('processo_seletivo.titulo')->label('Processo Seletivo'),
            ExportColumn::make('inscricao_vaga.codigo')->label('Cod Vaga'),
            ExportColumn::make('inscricao_vaga.descricao')->label('Descrição Vaga'),
            ExportColumn::make('tipo_vaga.descricao'),
            ExportColumn::make('necessita_atendimento'),
            ExportColumn::make('qual_atendimento'),
            ExportColumn::make('observacao'),
            ExportColumn::make('local_prova'),
            ExportColumn::make('link_inscricao')
                ->label('Link Inscrição')
                ->state(function (Inscricao $record): ?string {
                    try {
                        $url = route(
                            'filament.gps.resources.processos.inscritos',
                            [
                                $record->processo_seletivo->idprocesso_seletivo,
                                'tableSearch' => $record->cod_inscricao
                            ]
                        );

                        if (!$url || !$record) {
                            return null;
                        }

                        return '=HYPERLINK("' . $url . '", "Visualizar Inscrição")';
                    } catch (\Throwable $th) {
                        throw $th;
                        return '';
                    }
                }),
            // Dados Pessoa
            ExportColumn::make('inscricao_pessoa.nome')->label('Nome Candidato'),
            ExportColumn::make('inscricao_pessoa.nome_social')->label('Nome Social Candidato'),
            ExportColumn::make('inscricao_pessoa.identidade_genero')->label('Identidade de Gênero'),
            ExportColumn::make('inscricao_pessoa.sexo')->label('Sexo'),
            ExportColumn::make('inscricao_pessoa.cpf')->label('CPF'),
            ExportColumn::make('inscricao_pessoa.data_nascimento')->label('Data Nascimento'),
            ExportColumn::make('inscricao_pessoa.endereco')->label('Logradouro'),
            ExportColumn::make('inscricao_pessoa.bairro')->label('Bairro'),
            ExportColumn::make('inscricao_pessoa.numero')->label('Número'),
            ExportColumn::make('inscricao_pessoa.complemento')->label('Complemento'),
            ExportColumn::make('inscricao_pessoa.cidade')->label('Cidade'),
            ExportColumn::make('inscricao_pessoa.email')->label('Email'),
            ExportColumn::make('inscricao_pessoa.telefone')->label('Telefone'),
        ];
    }

    public static function getCompletedNotificationBody(Export $export): string
    {
        $body = 'A exportação de inscricao foi concluída e ' . number_format($export->successful_rows) . ' ' . str('linha')->plural($export->successful_rows) . ' foram exportadas.';

        if ($failedRowsCount = $export->getFailedRowsCount()) {
            $body .= ' ' . number_format($failedRowsCount) . ' ' . str('linha')->plural($failedRowsCount) . ' falharam na exportação.';
        }

        return $body;
    }
}

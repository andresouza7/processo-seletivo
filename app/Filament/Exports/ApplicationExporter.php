<?php

namespace App\Filament\Exports;

use Throwable;
use App\Models\Application;
use Filament\Actions\Exports\ExportColumn;
use Filament\Actions\Exports\Exporter;
use Filament\Actions\Exports\Models\Export;

class ApplicationExporter extends Exporter
{
    protected static ?string $model = Application::class;

    public static function getColumns(): array
    {
        return [
            // Dados Inscrição
            ExportColumn::make('link_inscricao')
                ->label('Link Inscrição')
                ->state(function (Application $record): ?string {
                    try {
                        $url = route(
                            'filament.gps.resources.processos.inscritos',
                            [
                                $record->process->id,
                                'search' => $record->code
                            ]
                        );

                        if (!$url || !$record) {
                            return null;
                        }

                        return '=HYPERLINK("' . $url . '", "Link")';
                    } catch (Throwable $th) {
                        throw $th;
                        return '';
                    }
                }),
            ExportColumn::make('code')->label('Cod Inscrição'),
            ExportColumn::make('process.title')->label('Processo Seletivo'),
            ExportColumn::make('position.code')->label('Cod Vaga'),
            ExportColumn::make('position.description')->label('Descrição Vaga'),
            ExportColumn::make('quota.description')->label('Cota'),
            ExportColumn::make('requires_assistance')->label('Necessida Atendimento'),
            ExportColumn::make('assistance_details')->label('Qual Atendimento'),

            // Dados Pessoa
            ExportColumn::make('candidate.name')->label('Nome Candidato'),
            ExportColumn::make('candidate.social_name')->label('Nome Social Candidato'),
            ExportColumn::make('candidate.gender_identity')->label('Identidade de Gênero'),
            ExportColumn::make('candidate.disability')->label('Deficiência'),
            ExportColumn::make('candidate.race')->label('Raça'),
            ExportColumn::make('candidate.sexual_orientation')->label('Orientação Sexual'),
            ExportColumn::make('candidate.marital_status')->label('Estado Civil'),
            ExportColumn::make('candidate.community')->label('Comunidade'),
            ExportColumn::make('candidate.sex')->label('Sexo'),
            ExportColumn::make('candidate.cpf')->label('CPF'),
            ExportColumn::make('candidate.birth_date')->label('Data Nascimento'),
            ExportColumn::make('candidate.address')->label('Logradouro'),
            ExportColumn::make('candidate.district')->label('Bairro'),
            ExportColumn::make('candidate.address_number')->label('Número'),
            ExportColumn::make('candidate.address_complement')->label('Complemento'),
            ExportColumn::make('candidate.city')->label('Cidade'),
            ExportColumn::make('candidate.email')->label('Email'),
            ExportColumn::make('candidate.phone')->label('Telefone'),
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

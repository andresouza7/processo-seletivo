<?php

namespace App\Filament\Exports;

use Filament\Actions\Exports\ExportColumn;
use Filament\Actions\Exports\Exporter;
use Filament\Actions\Exports\Models\Export;
use Spatie\Activitylog\Models\Activity;

class ActivityExporter extends Exporter
{
    protected static ?string $model = Activity::class;

    public static function getColumns(): array
    {
        return [
            ExportColumn::make('event')->label('Evento'),
            ExportColumn::make('subject_type')->label('Entidade'),
            ExportColumn::make('subject_id')->label('ID Registro da Entidade'),
            ExportColumn::make('causer.name')->label('Autor Usuário'),
            ExportColumn::make('causer.nome')->label('Autor Candidato'),
            ExportColumn::make('causer_id')->label('ID Autor'),
            ExportColumn::make('properties')->label('Alterações'),
            ExportColumn::make('created_at')->label('Timestamp'),
        ];
    }

    public static function getCompletedNotificationBody(Export $export): string
    {
        $body = 'A exportação dos logs foi concluída e ' . number_format($export->successful_rows) . ' ' . str('linha')->plural($export->successful_rows) . ' foram exportadas.';

        if ($failedRowsCount = $export->getFailedRowsCount()) {
            $body .= ' ' . number_format($failedRowsCount) . ' ' . str('linha')->plural($failedRowsCount) . ' falharam na exportação.';
        }

        return $body;
    }
}

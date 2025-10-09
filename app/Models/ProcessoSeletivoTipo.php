<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Spatie\Activitylog\LogOptions;
use Spatie\Activitylog\Traits\LogsActivity;

class ProcessoSeletivoTipo extends Model
{
    use HasFactory, LogsActivity;

    public function getActivitylogOptions(): LogOptions
    {
        return LogOptions::defaults()
            ->logFillable()
            ->dontSubmitEmptyLogs();
    }

    protected $primaryKey = 'idprocesso_seletivo_tipo';

    protected $table = 'processo_seletivo_tipo';

    protected $fillable = [
        'description',
        'slug'
    ];
}

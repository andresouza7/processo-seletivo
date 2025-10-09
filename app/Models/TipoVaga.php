<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Spatie\Activitylog\LogOptions;
use Spatie\Activitylog\Traits\LogsActivity;

class TipoVaga extends Model
{
    use HasFactory, LogsActivity;

    public function getActivitylogOptions(): LogOptions
    {
        return LogOptions::defaults()
            ->logFillable()
            ->dontSubmitEmptyLogs();
    }

    protected $table = 'tipo_vaga';

    protected $primaryKey = 'id_tipo_vaga';

    protected $fillable = [
        'description'n',
    ];

    // public function processo_seletivo()
    // {
    //     return $this->belongsTo(ProcessoSeletivo::class, 'psel_id');
    // }

    // public function inscricoes()
    // {
    //     return $this->hasMany(Inscricao::class, 'vaga_id');
    // }
}

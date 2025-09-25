<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Spatie\Activitylog\LogOptions;
use Spatie\Activitylog\Traits\LogsActivity;
use Spatie\MediaLibrary\HasMedia;
use Spatie\MediaLibrary\InteractsWithMedia;

class Recurso extends Model implements HasMedia
{
    use HasFactory, LogsActivity, InteractsWithMedia;

    public function getActivitylogOptions(): LogOptions
    {
        return LogOptions::defaults()
            ->logFillable()
            ->dontSubmitEmptyLogs();
    }

    protected static function booted()
    {
        static::deleting(function ($model) {
            $model->clearMediaCollection(); // remove todos os arquivos associados
        });
    }

    protected $table = 'recursos';

    protected $primaryKey = 'idrecurso';

    protected $fillable = [
        'descricao',
        'resposta',
        'idprocesso_seletivo',
        'idinscricao_pessoa',
        'idinscricao',
        'idetapa_recurso',
        'situacao',
        'data_hora'
    ];

    // public $timestamps = false;

    public function getRespostaUrlAttribute()
    {
        if (!$this->hasMedia('anexo_resposta_recurso')) return null;

        return $this->getFirstMediaUrl('anexo_resposta_recurso');
    }

    public function inscricao()
    {
        return $this->belongsTo(Inscricao::class, 'idinscricao');
    }

    public function processo_seletivo()
    {
        return $this->belongsTo(ProcessoSeletivo::class, 'idprocesso_seletivo', 'idprocesso_seletivo');
    }

    public function inscricao_pessoa()
    {
        return $this->belongsTo(InscricaoPessoa::class, 'idinscricao_pessoa', 'idpessoa');
    }

    public function etapa_recurso()
    {
        return $this->belongsTo(EtapaRecurso::class, 'idetapa_recurso', 'idetapa_recurso');
    }
}

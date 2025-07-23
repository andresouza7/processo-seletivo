<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class EtapaRecurso extends Model
{
    use HasFactory;

    protected $primaryKey = 'idetapa_recurso';

    protected $table = 'etapa_recurso';

    protected $fillable = [
        'descricao',
        'data_inicio_recebimento',
        'data_fim_recebimento',
        'data_inicio_resultado',
        'data_fim_resultado',
        'requer_anexos',
        'permite_multiplos_recursos',
        'idprocesso_seletivo',
    ];

     protected $appends = [
        'aceita_recurso',
        'resultado_disponivel'
    ];

    // public $timestamps = false;

    public function getAceitaRecursoAttribute()
    {
        $today = now()->toDateString(); // Get current date in 'Y-m-d' format

        return $this->data_inicio_recebimento <= $today && $this->data_inicio_recebimento >= $today;
    }

    public function getResultadoDisponivelAttribute()
    {
        $today = now()->toDateString();

        return $this->data_inicio_resultado <= $today && $this->data_inicio_resultado >= $today;
    }

    public function processo_seletivo()
    {
        return $this->belongsTo(ProcessoSeletivo::class, 'idprocesso_seletivo', 'idprocesso_seletivo');
    }

    public function recursos()
    {
        return $this->hasMany(Recurso::class, 'idetapa_recurso', 'idetapa_recurso');
    }
}

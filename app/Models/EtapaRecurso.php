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
        'description',
        'submission_start_date',
        'submission_end_date',
        'result_start_date',
        'result_end_date',
        'has_attachments',
        'allow_many',
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

        return $this->submission_start_date <= $today && $this->submission_start_date >= $today;
    }

    public function getResultadoDisponivelAttribute()
    {
        $today = now()->toDateString();

        return $this->result_start_date <= $today && $this->result_start_date >= $today;
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

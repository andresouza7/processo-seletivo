<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class InscricaoVaga extends Model
{
    use HasFactory;

    protected $table = 'inscricao_vagas';

    protected $primaryKey = 'idinscricao_vaga';

    protected $fillable = [
        'idprocesso_seletivo',
        'code',
        'description'
    ];

    // public $timestamps = false;

    public function processo_seletivo()
    {
        return $this->belongsTo(ProcessoSeletivo::class, 'idprocesso_seletivo', 'idprocesso_seletivo');
    }
}

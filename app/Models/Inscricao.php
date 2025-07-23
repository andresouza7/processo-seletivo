<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Spatie\Activitylog\LogOptions;
use Spatie\Activitylog\Traits\LogsActivity;
use Spatie\MediaLibrary\HasMedia;
use Spatie\MediaLibrary\InteractsWithMedia;

class Inscricao extends Model implements HasMedia
{
    use HasFactory, InteractsWithMedia, LogsActivity;

    public function getActivitylogOptions(): LogOptions
    {
        return LogOptions::defaults()
            ->logFillable()
            ->dontSubmitEmptyLogs();
    }

    protected $table = 'inscricao';

    protected $primaryKey = 'idinscricao';

    protected $fillable = [
        'cod_inscricao',
        'idprocesso_seletivo',
        'idinscricao_vaga',
        'idinscricao_pessoa',
        'idtipo_vaga',
        'data_hora',
        'necessita_atendimento',
        'qual_atendimento',
        'observacao',
        'local_prova',
        'ano_enem',
        'bonificacao'
    ];

    // public $timestamps = false;

    protected static function booted()
    {
        static::deleting(function ($model) {
            $model->clearMediaCollection(); // remove todos os arquivos associados
        });
    }

    public function processo_seletivo()
    {
        return $this->belongsTo(ProcessoSeletivo::class, 'idprocesso_seletivo', 'idprocesso_seletivo');
    }

    public function inscricao_vaga()
    {
        return $this->belongsTo(InscricaoVaga::class, 'idinscricao_vaga', 'idinscricao_vaga');
    }

    public function inscricao_pessoa()
    {
        return $this->belongsTo(InscricaoPessoa::class, 'idinscricao_pessoa', 'idpessoa');
    }

    public function tipo_vaga()
    {
        return $this->belongsTo(TipoVaga::class, 'idtipo_vaga', 'id_tipo_vaga');
    }

    public function recursos()
    {
        return $this->hasMany(Recurso::class, 'idinscricao', 'idinscricao');
    }

    public static function gerarCodigoUnico(): int
    {
        do {
            // Generate a 10-digit random number
            $uniqueId = rand(1000000000, 9999999999);

            // Check if the cod_inscricao already exists in the inscricao table
            $exists = Inscricao::where('cod_inscricao', $uniqueId)->exists();
        } while ($exists);

        return $uniqueId;
    }

    public static function generateUniqueCode()
    {
        do {
            $code = str_pad(random_int(0, 9999999999), 10, '0', STR_PAD_LEFT);
        } while (Inscricao::where('cod_inscricao', $code)->exists());

        return $code;
    }
}

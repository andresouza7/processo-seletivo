<?php

namespace App\Models;

use App\Filament\Candidato\Resources\EtapaRecursos\EtapaRecursoResource;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Facades\Storage;
use Spatie\Activitylog\LogOptions;
use Spatie\Activitylog\Traits\LogsActivity;

class ProcessoSeletivo extends Model
{
    use HasFactory, LogsActivity;

    public function getActivitylogOptions(): LogOptions
    {
        return LogOptions::defaults()
            ->logFillable()
            ->dontSubmitEmptyLogs();
    }

    protected $table = 'processo_seletivo';

    protected $primaryKey = 'idprocesso_seletivo';

    protected $fillable = [
        'idprocesso_seletivo',
        'idprocesso_seletivo_tipo',
        'titulo',
        'descricao',
        'numero',
        'data_criacao',
        'situacao',
        'acessos',
        'publicado',
        'diretorio',
        'data_publicacao_inicio',
        'data_publicacao_fim',
        'data_inscricao_inicio',
        'data_inscricao_fim',
        'data_recurso_inicio',
        'data_recurso_fim',
        'psu',
        'possui_isencao',
        'anexos'
    ];

    protected $appends = [
        'aceita_inscricao',
        'aceita_recurso',
        'link_recurso'
    ];

    protected $casts = [
        'anexos' => 'array'
    ];

    protected static function booted()
    {
        static::deleting(function ($processo) {
            $processo->inscricoes()->each(function ($inscricao) {
                $inscricao->clearMediaCollection('documentos_requeridos');
            });
        });
    }

    // public $timestamps = false;

    public function getUrlArquivoAttribute()
    {
        $systemMigrationReferenceDate = Carbon::parse('2024-11-01');

        // Check if `data_publicacao` is older than the reference date
        if (Carbon::parse($this->data_publicacao)->lt($systemMigrationReferenceDate)) {
            $oldFilePath = $this->processo_seletivo->tipo->chave . '/' .
                $this->processo_seletivo->diretorio . '/' .
                optional($this->arquivo)->codname . '.pdf';

            return Storage::url($oldFilePath);
        }

        // Otherwise, return the URL from Spatie Media Library
        return $this->getFirstMediaUrl();
    }

    public function getAceitaInscricaoAttribute()
    {
        $today = now()->toDateString(); // Get current date in 'Y-m-d' format

        return $this->data_inscricao_inicio <= $today && $this->data_inscricao_fim >= $today;
    }

    public function getAceitaRecursoAttribute()
    {
        $today = now()->toDateString(); // 'Y-m-d'

        return $this->etapa_recurso()
            ->whereDate('data_inicio_recebimento', '<=', $today)
            ->whereDate('data_fim_recebimento', '>=', $today)
            ->exists();
    }

    public function getLinkRecursoAttribute()
    {
        $etapa = EtapaRecurso::where('idprocesso_seletivo', $this->idprocesso_seletivo)->orderBy('idetapa_recurso', 'desc')->first();

        return $etapa ? route('filament.candidato.resources.etapa-recursos.edit', $etapa->idetapa_recurso) : null;
    }

    public function tipo()
    {
        return $this->belongsTo(ProcessoSeletivoTipo::class, 'idprocesso_seletivo_tipo', 'idprocesso_seletivo_tipo');
    }

    public function anexos()
    {
        return $this->hasMany(ProcessoSeletivoAnexo::class, 'idprocesso_seletivo', 'idprocesso_seletivo');
    }

    public function inscricoes()
    {
        return $this->hasMany(Inscricao::class, 'idprocesso_seletivo', 'idprocesso_seletivo');
    }

    public function inscricao_vaga()
    {
        return $this->hasMany(InscricaoVaga::class, 'idprocesso_seletivo', 'idprocesso_seletivo');
    }

    public function recursos()
    {
        return $this->hasMany(Recurso::class, 'idprocesso_seletivo', 'idprocesso_seletivo');
    }

    public function etapa_recurso()
    {
        return $this->hasMany(EtapaRecurso::class, 'idprocesso_seletivo', 'idprocesso_seletivo');
    }

    public function avaliadores()
    {
        return $this->belongsToMany(
            User::class,
            'avaliador_processo_seletivo',
            'idprocesso_seletivo',
            'user_id',
            'idprocesso_seletivo'
        );
        // ->whereHas('roles', function ($query) {
        //     $query->where('name', 'avaliador');
        // });
    }

    public function scopeInscricoesAbertas(Builder $query): void
    {
        $query->where('publicado', 'S')
            ->whereDate('data_inscricao_inicio', '<=', now())
            ->whereDate('data_inscricao_fim', '>=', now());
    }

    public function scopeEmAndamento(Builder $query): void
    {
        $query->where('publicado', 'S')
            ->whereDate('data_publicacao_inicio', '<=', now())
            ->whereDate('data_publicacao_fim', '>=', now());
    }

    public function scopeFinalizados(Builder $query): void
    {
        $query->where('publicado', 'S')
            ->whereDate('data_publicacao_fim', '<=', now());
    }
}

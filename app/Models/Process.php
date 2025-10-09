<?php

namespace App\Models;

use App\Filament\Candidato\Resources\EtapaRecursos\EtapaRecursoResource;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Facades\Storage;
use Spatie\Activitylog\LogOptions;
use Spatie\Activitylog\Traits\LogsActivity;

class Process extends Model
{
    use SoftDeletes, HasFactory, LogsActivity;

    public function getActivitylogOptions(): LogOptions
    {
        return LogOptions::defaults()
            ->logFillable()
            ->dontSubmitEmptyLogs();
    }

    protected $fillable = [
        'process_type_id',
        'title',
        'description',
        'number',
        'document_date',
        'status',
        'views',
        'is_published',
        'directory',
        'publication_start_date',
        'publication_end_date',
        'application_start_date',
        'application_end_date',
        'has_fee_exemption',
        'attachment_fields'
    ];

    protected $appends = [
        'can_apply',
        'can_appeal',
        'appeal_link'
    ];

    protected $casts = [
        'attachment_fields' => 'array'
    ];

    protected static function booted()
    {
        static::deleting(function ($processo) {
            $processo->applications()->each(function ($application) {
                $application->clearMediaCollection('documentos_requeridos');
            });
        });
    }

    // public $timestamps = false;

    public function getUrlArquivoAttribute()
    {
        $systemMigrationReferenceDate = Carbon::parse('2024-11-01');

        // Check if `data_publicacao` is older than the reference date
        if (Carbon::parse($this->publication_date)->lt($systemMigrationReferenceDate)) {
            $oldFilePath = $this->process->tipo->slug . '/' .
                $this->process->directory . '/' .
                optional($this->arquivo)->codname . '.pdf';

            return Storage::url($oldFilePath);
        }

        // Otherwise, return the URL from Spatie Media Library
        return $this->getFirstMediaUrl();
    }

    public function getCanApplyAttribute()
    {
        $today = now()->toDateString(); // Get current date in 'Y-m-d' format

        return $this->application_start_date <= $today && $this->application_end_date >= $today;
    }

    public function getCanAppealAttribute()
    {
        $today = now()->toDateString(); // 'Y-m-d'

        return $this->appeal_stage()
            ->whereDate('submission_start_date', '<=', $today)
            ->whereDate('submission_end_date', '>=', $today)
            ->exists();
    }

    public function getAppealLinkAttribute()
    {
        $stage = AppealStage::where('id', $this->id)->orderBy('id', 'desc')->first();

        return $stage ? route('filament.candidato.resources.etapa-recursos.edit', $stage->id) : null;
    }

    public function type()
    {
        return $this->belongsTo(ProcessType::class);
    }

    public function attachments()
    {
        return $this->hasMany(ProcessAttachment::class);
    }

    public function applications()
    {
        return $this->hasMany(Application::class);
    }

    public function position()
    {
        return $this->hasMany(Position::class);
    }

    public function appeals()
    {
        return $this->hasMany(Appeal::class);
    }

    public function appeal_stage()
    {
        return $this->hasMany(AppealStage::class);
    }

    public function evaluators()
    {
        return $this->belongsToMany(User::class);
        // ->whereHas('roles', function ($query) {
        //     $query->where('name', 'avaliador');
        // });
    }

    public function scopeInscricoesAbertas(Builder $query): void
    {
        $query->where('is_published', true)
            ->whereDate('application_start_date', '<=', now())
            ->whereDate('application_end_date', '>=', now());
    }

    public function scopeEmAndamento(Builder $query): void
    {
        $query->where('is_published', true)
            ->whereDate('publication_start_date', '<=', now())
            ->whereDate('publication_end_date', '>=', now());
    }

    public function scopeFinalizados(Builder $query): void
    {
        $query->where('is_published', true)
            ->whereDate('publication_end_date', '<=', now());
    }
}

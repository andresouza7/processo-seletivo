<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Storage;
use Spatie\Activitylog\LogOptions;
use Spatie\Activitylog\Traits\LogsActivity;
use Spatie\Permission\Traits\HasRoles;

class Process extends Model
{
    use SoftDeletes, HasFactory, LogsActivity, HasRoles;

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
        'status',
        'views',
        'is_published',
        'directory',
        'publication_start_date',
        'publication_end_date',
        'application_start_date',
        'application_end_date',
        'has_fee',
        'multiple_applications',
        'attachment_fields'
    ];

    protected $casts = [
        'attachment_fields' => 'array'
    ];

    protected $guard_name = 'web';

    protected static function booted()
    {
        static::deleting(function ($processo) {
            $processo->applications()->each(function ($application) {
                $application->clearMedia();
            });
        });

        static::saved(function () {
            Cache::forget('processos_inscricoes_abertas_options');
        });

        static::deleted(function () {
            Cache::forget('processos_inscricoes_abertas_options');
        });
    }

    public function type()
    {
        // *fk manually declared because its different than method name
        return $this->belongsTo(ProcessType::class, 'process_type_id');
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

    public function scopeInscricoesAbertas(Builder $query): void
    {
        $query->where('is_published', true)
            ->whereDate('application_start_date', '<=', now())
            ->whereDate('application_end_date', '>=', now())
            ->has('position');
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

    public function scopeRecursoAberto(Builder $query): void
    {
        $query->whereHas('appeal_stage', function ($sub) {
            $sub->whereDate('submission_start_date', '<=', now())
                ->whereDate('submission_end_date', '>=', now());
        });
    }

    public function activeAppealStage(): ?AppealStage
    {
        return $this->appeal_stage()
            ->whereDate('submission_end_date', '>=', now())
            ->first();
    }
}

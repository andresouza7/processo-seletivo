<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Spatie\Activitylog\LogOptions;
use Spatie\Activitylog\Traits\LogsActivity;
use Spatie\MediaLibrary\HasMedia;
use Spatie\MediaLibrary\InteractsWithMedia;

class Application extends Model implements HasMedia
{
    use SoftDeletes, HasFactory, InteractsWithMedia, LogsActivity;

    public function getActivitylogOptions(): LogOptions
    {
        return LogOptions::defaults()
            ->logFillable()
            ->dontSubmitEmptyLogs();
    }

    protected $fillable = [
        'code',
        'process_id',
        'position_id',
        'candidate_id',
        'quota_id',
        'submitted_at',
        'requires_assistance',
        'assistance_details',
    ];

    protected static function booted()
    {
        static::deleting(function ($model) {
            $model->clearMediaCollection('documentos_requeridos'); // remove todos os arquivos associados
        });
    }

    public function process()
    {
        return $this->belongsTo(Process::class);
    }

    public function position()
    {
        return $this->belongsTo(Position::class);
    }

    public function candidate()
    {
        return $this->belongsTo(Candidate::class);
    }

    public function quota()
    {
        return $this->belongsTo(Quota::class);
    }

    public function appeals()
    {
        return $this->hasMany(Appeal::class);
    }

    public static function generateUniqueCode(): int
    {
        do {
            $uniqueId = rand(1000000000, 9999999999);
            $exists = Application::where('code', $uniqueId)->exists();
        } while ($exists);

        return $uniqueId;
    }

    public function activeAppealStage(): ?AppealStage
    {
        return $this->process->appeal_stage()->latest()->first();
    }

    public function scopeAppealSubmissionOpen($query)
    {
        $today = now()->toDateString();

        return $query->where(function ($q) {
            $q->whereDoesntHave('appeals', function ($q) {
                // No appeal for the latest appeal stage
                $q->whereIn('appeal_stage_id', function ($sub) {
                    $sub->selectRaw('MAX(id)')
                        ->from('appeal_stages as s')
                        ->whereColumn('s.process_id', 'appeals.process_id');
                });
            })
                ->orWhereHas('process.appeal_stage', function ($q) {
                    // The process has a stage that allows multiple appeals
                    $q->whereExists(function ($sub) {
                        $sub->selectRaw(1)
                            ->from('appeal_stages as s')
                            ->whereColumn('s.process_id', 'process_id')
                            ->where('s.allow_many', true);
                    });
                });
        })
            // mas o processo da inscrição deve ter uma etapa ativa aberta hoje
            ->whereHas('process.appeal_stage', function ($q) use ($today) {
                $q->where(function ($q) use ($today) {
                    $q->whereDate('submission_start_date', '<=', $today)
                        ->whereDate('submission_end_date', '>=', $today);
                });
            });
    }
}

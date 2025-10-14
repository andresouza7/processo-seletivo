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

    public function canAppeal()
    {
        $stage = $this->activeAppealStage();
        if (!$stage || !$stage->accepts_appeal) {
            return false;
        }

        $appealExists = $this->appeals()->where('appeal_stage_id', $stage->id)->exists();

        return !$appealExists || $stage->allow_many; // true se existe uma etapa com recurso aberto e o usuário não fez um recurso pra ela
    }

    public function scopeCanAppeal($query)
    {
        $today = now()->toDateString();

        return $query
            ->whereDoesntHave('appeals')
            ->whereHas('process.appeal_stage', function ($q) use ($today) {
                $q->where(function ($q) use ($today) {
                    $q->whereDate('submission_start_date', '<=', $today)
                        ->whereDate('submission_end_date', '>=', $today);
                });
            });
    }

    public function scopeCanViewAppeal($query)
    {
        $today = now()->toDateString();

        return $query
            ->whereHas('process.appeal_stage', function ($q) use ($today) {
                $q->where(function ($q) use ($today) {
                    $q->whereDate('result_start_date', '<=', $today)
                        ->whereDate('result_end_date', '>=', $today);
                });
            });
    }
}

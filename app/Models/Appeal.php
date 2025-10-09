<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Spatie\Activitylog\LogOptions;
use Spatie\Activitylog\Traits\LogsActivity;
use Spatie\MediaLibrary\HasMedia;
use Spatie\MediaLibrary\InteractsWithMedia;

class Appeal extends Model implements HasMedia
{
    use SoftDeletes, HasFactory, LogsActivity, InteractsWithMedia;

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

    protected $fillable = [
        'description',
        'response',
        'process_id',
        'candidate_id',
        'application_id',
        'appeal_stage_id',
        'result',
        'submitted_at'
    ];

    public function application()
    {
        return $this->belongsTo(Application::class);
    }

    public function process()
    {
        return $this->belongsTo(Process::class);
    }

    public function candidate()
    {
        return $this->belongsTo(Candidate::class);
    }

    public function appeal_stage()
    {
        return $this->belongsTo(AppealStage::class);
    }
}

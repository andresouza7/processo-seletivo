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

    protected $fillable = [
        'text',
        'process_id',
        'candidate_id',
        'application_id',
        'appeal_stage_id',
        'evaluator_id',
        'evaluated_at',
        'response',
        'result',
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

    public function evaluator()
    {
        return $this->belongsTo(User::class, 'evaluator_id');
    }

    public function scopeSubmitted($query)
    {
        $today = now()->toDateString();

        return $query->whereHas('appeal_stage', function ($query) use ($today) {
            $query->whereDate('result_start_date', '>', $today);
        });
    }

    public function scopeAvailable($query)
    {
        $today = now()->toDateString();

        return $query->whereHas('appeal_stage', function ($q) use ($today) {
            $q->whereDate('result_start_date', '<=', $today)
                ->whereDate('result_end_date', '>=', $today);
        })->whereNotNull('result')->whereNotNull('response');
    }

    public function hasResult()
    {
        $today = now()->toDateString();

        return $this->appeal_stage->result_start_date <= $today && $this->appeal_stage->result_end_date >= $today;
    }
}

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
}

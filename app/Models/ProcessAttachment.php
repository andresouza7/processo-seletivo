<?php

namespace App\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Facades\Storage;
use Spatie\Activitylog\LogOptions;
use Spatie\Activitylog\Traits\LogsActivity;
use Spatie\MediaLibrary\HasMedia;
use Spatie\MediaLibrary\InteractsWithMedia;

class ProcessAttachment extends Model implements HasMedia
{
    use SoftDeletes, HasFactory, InteractsWithMedia, LogsActivity;

    public function getActivitylogOptions(): LogOptions
    {
        return LogOptions::defaults()
            ->logOnly(['description'])
            ->dontSubmitEmptyLogs();
    }

    protected $fillable = [
        'process_id',
        'idarquivo',
        'description',
        'views'
    ];

    protected $appends = [
        'file_url'
    ];

    public function getFileUrlAttribute()
    {
        return route('media.view', $this->id);
    }

    public function process()
    {
        return $this->belongsTo(Process::class);
    }

    public function arquivo()
    {
        return $this->belongsTo(Arquivo::class, 'idarquivo', 'idarquivo');
    }
}

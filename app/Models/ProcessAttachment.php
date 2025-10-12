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
            ->logOnly(['description', 'publication_date'])
            ->dontSubmitEmptyLogs();
    }

    protected $fillable = [
        'process_id',
        'idarquivo',
        'description',
        'publication_date',
        'views'
    ];

    protected $appends = [
        'file_url'
    ];

    protected $dates = [
        'publication_date'
    ];

    public function getFileUrlAttribute()
    {
        $systemMigrationReferenceDate = Carbon::parse('2024-11-01');

        // Check if `data_publicacao` is older than the reference date
        if (Carbon::parse($this->created_at)->lt($systemMigrationReferenceDate)) {
            
            $oldFilePath = $this->process->type->slug . '/' .
                $this->process->directory . '/' .
                optional($this->arquivo)->codname . '.pdf';

            return Storage::url($oldFilePath);
        }

        // Otherwise, return the URL from Spatie Media Library
        return $this->getFirstMediaUrl();
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

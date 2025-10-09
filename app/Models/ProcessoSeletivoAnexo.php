<?php

namespace App\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Storage;
use Spatie\Activitylog\LogOptions;
use Spatie\Activitylog\Traits\LogsActivity;
use Spatie\MediaLibrary\HasMedia;
use Spatie\MediaLibrary\InteractsWithMedia;

class ProcessoSeletivoAnexo extends Model implements HasMedia
{
    use HasFactory, InteractsWithMedia, LogsActivity;

    public function getActivitylogOptions(): LogOptions
    {
        return LogOptions::defaults()
            ->logOnly(['description', 'publication_date'])
            ->dontSubmitEmptyLogs();
    }

    protected $table = 'processo_seletivo_anexo';

    protected $primaryKey = 'idprocesso_seletivo_anexo';

    protected $fillable = [
        'idprocesso_seletivo_anexo',
        'idprocesso_seletivo',
        'idarquivo',
        'description',
        'publication_date',
        'views'
    ];

    protected $appends = [
        'url_arquivo'
    ];

    protected $dates = [
        'publication_date'
    ];

    public function getUrlArquivoAttribute()
    {
        $systemMigrationReferenceDate = Carbon::parse('2024-11-01');

        // Check if `data_publicacao` is older than the reference date
        if (Carbon::parse($this->publication_date)->lt($systemMigrationReferenceDate)) {
            $oldFilePath = $this->processo_seletivo->tipo->slug . '/' .
                $this->processo_seletivo->directory . '/' .
                optional($this->arquivo)->codname . '.pdf';

            return Storage::url($oldFilePath);
        }

        // Otherwise, return the URL from Spatie Media Library
        return $this->getFirstMediaUrl();
    }

    // public $timestamps = false;

    public function processo_seletivo()
    {
        return $this->belongsTo(ProcessoSeletivo::class, 'idprocesso_seletivo', 'idprocesso_seletivo');
    }

    public function arquivo()
    {
        return $this->belongsTo(Arquivo::class, 'idarquivo', 'idarquivo');
    }
}

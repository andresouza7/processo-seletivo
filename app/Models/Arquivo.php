<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Storage;

class Arquivo extends Model
{
    use HasFactory;

    protected $table = 'arquivo';

    protected $primaryKey = 'idarquivo';

    protected $fillable = [
        'size',
        'width',
        'height',
        'name',
        'mimetype',
        'description',
        'codname'
    ];

    public $timestamps = false;
    
    public function process_attachment() {
        return $this->hasOne(ProcessAttachment::class, 'idarquivo', 'idarquivo');
    }
}

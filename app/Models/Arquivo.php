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
        'descricao',
        'codname'
    ];

    public $timestamps = false;
    
    public function processo_seletivo_anexo() {
        return $this->hasOne(ProcessoSeletivoAnexo::class, 'idarquivo', 'idarquivo');
    }
}

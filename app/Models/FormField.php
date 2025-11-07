<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class FormField extends Model
{
    use HasFactory;

    protected $fillable = [
        'process_id',
        'label',
        'name',
        'type',
        'required',
        'options',
        'order',
        'helper_text'
    ];

    protected $casts = [
        'required' => 'boolean',
        'options' => 'array',
    ];

    public function process()
    {
        return $this->belongsTo(Process::class);
    }
}

<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class AppealStage extends Model
{
    use SoftDeletes, HasFactory;

    protected $fillable = [
        'description',
        'submission_start_date',
        'submission_end_date',
        'result_start_date',
        'result_end_date',
        'has_attachments',
        'allow_many',
        'process_id',
    ];

     protected $appends = [
        'can_appeal',
        'has_result'
    ];

    public function getCanAppealAttribute()
    {
        $today = now()->toDateString(); // Get current date in 'Y-m-d' format

        return $this->submission_start_date <= $today && $this->submission_start_date >= $today;
    }

    public function getHasResultAttribute()
    {
        $today = now()->toDateString();

        return $this->result_start_date <= $today && $this->result_start_date >= $today;
    }

    public function process()
    {
        return $this->belongsTo(Process::class);
    }

    public function appeals()
    {
        return $this->hasMany(Appeal::class);
    }
}

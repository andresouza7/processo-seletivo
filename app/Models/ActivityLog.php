<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ActivityLog extends Model
{
    protected $table = 'activity_log';

    protected $casts = [
        'properties' => 'array',
    ];

    /**
     * The model that was acted upon.
     */
    public function subject()
    {
        return $this->morphTo();
    }

    /**
     * The model responsible for the activity.
     */
    public function causer()
    {
        return $this->morphTo();
    }
}

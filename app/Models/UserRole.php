<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Spatie\Permission\Models\Role;

class UserRole extends Model
{
    protected $fillable = [
        'user_id',
        'role_id',
        'created_at',
        'revoked_at',
        'create_doc',
        'revoke_doc'
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function role()
    {
        return $this->belongsTo(Role::class);
    }

    public function isExpired(int $days = 30): bool
    {
        if ($this->revoked_at) {
            return true;
        }

        return $this->created_at->addMinute()->isPast();
    }
}

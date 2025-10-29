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
        'expires_at',
        'create_doc',
        'revoke_doc'
    ];

    protected $casts = [
        'expires_at' => 'datetime'
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
        return $this->expires_at->isPast();
    }
}

<?php

namespace App\Models;

use Filament\Models\Contracts\FilamentUser;
use Filament\Models\Contracts\HasName;
use Filament\Panel;
use Illuminate\Auth\Passwords\CanResetPassword;
use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class Candidate extends Authenticatable implements HasName, FilamentUser
{
    use SoftDeletes, HasFactory, Notifiable, CanResetPassword;

    protected $fillable = [
        'name',
        'social_name',
        'mother_name',
        'birth_date',
        'sex',
        'rg',
        'cpf',
        //sociais
        'gender_identity',
        'gender_identity_description',
        'sexual_orientation',
        //novos
        'race',
        'has_disability',
        'disability_description',
        'marital_status',
        'community',
        //endereco
        'address',
        'postal_code',
        'district',
        'address_number',
        'address_complement',
        'city',
        'phone',
        'email',
        'password',
    ];

    protected $hidden = [
        'password',
        'remember_token'
    ];

    protected function casts(): array
    {
        return [
            'password' => 'hashed',
        ];
    }

    public function getFilamentName(): string
    {
        preg_match('/^\S+/', trim($this->name), $matches);

        return $matches[0] ?? $this->name;
    }

    public function canAccessPanel(Panel $panel): bool
    {
        return true;
    }

    public function applications()
    {
        return $this->hasMany(Application::class);
    }

    protected const CAMPOS_OBRIGATORIOS = [
        'mother_name',
        'birth_date',
        'sex',
        'rg',
        'email',
        'postal_code',
        'address',
        'address_number',
        'district',
        'city',
        'phone'
    ];

    public function hasMissingData(): bool
    {
        return collect(self::CAMPOS_OBRIGATORIOS)
            ->contains(fn($campo) => is_null($this->{$campo}));
    }
}

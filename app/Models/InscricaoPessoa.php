<?php

namespace App\Models;

use Filament\Models\Contracts\FilamentUser;
use Filament\Models\Contracts\HasName;
use Filament\Panel;
use Illuminate\Auth\Passwords\CanResetPassword;
use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class InscricaoPessoa extends Authenticatable implements HasName, FilamentUser
{
    use HasFactory, Notifiable, CanResetPassword;

    protected $table = 'inscricao_pessoa';

    protected $primaryKey = 'idpessoa';

    protected $fillable = [
        'idpessoa',
        'name',
        'social_name',
        'mother_name',
        'birth_date',
        'sex',
        'rg',
        'cpf',
        'matricula',
        'address',
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
        'postal_code',
        'district',
        'address_number',
        'address_complement',
        'city',
        'phone',
        'email',
        'senha',
        'password',
        'perfil',
        'situacao',
        'link_lattes',
        'resumo'
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

    // public $timestamps = false; 

    // public function getAuthPassword()
    // {
    //     return $this->senha;
    // }

    // public function setSenhaAttribute($value)
    // {
    //     $this->attributes['senha'] = bcrypt($value);
    //     $this->attributes['senha'] = md5($value);
    // }

    public function getFilamentName(): string
    {
        return $this->name;
    }

    public function canAccessPanel(Panel $panel): bool
    {
        return true;
    }

    public function inscricoes()
    {
        return $this->hasMany(Inscricao::class, 'idinscricao_pessoa', 'idpessoa');
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

    public function possuiDadosPendentes(): bool
    {
        return collect(self::CAMPOS_OBRIGATORIOS)
            ->contains(fn($campo) => is_null($this->{$campo}));
    }
}

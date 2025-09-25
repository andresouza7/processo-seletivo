<?php

namespace App\Models;

use Filament\Models\Contracts\HasName;
use Illuminate\Auth\Passwords\CanResetPassword;
use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

// add must verify email directive later
class InscricaoPessoa extends Authenticatable implements HasName
{
    use HasFactory, Notifiable, CanResetPassword;

    protected $table = 'inscricao_pessoa';

    protected $primaryKey = 'idpessoa';

    protected $fillable = [
        'idpessoa',
        'nome',
        'nome_social',
        'mae',
        'data_nascimento',
        'orientacao_sexual',
        'identidade_genero',
        'sexo',
        'ci',
        'cpf',
        'matricula',
        'endereco',
        'cep',
        'bairro',
        'numero',
        'complemento',
        'cidade',
        'telefone',
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
        return $this->nome;
    }

    public function inscricoes() {
        return $this->hasMany(Inscricao::class, 'idinscricao_pessoa', 'idpessoa');
    }
}

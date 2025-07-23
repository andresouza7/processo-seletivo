<?php

namespace Database\Factories;

use App\Models\Inscricao;
use App\Models\Pessoa;
use App\Models\ProcessoSeletivo;
use App\Models\User;
use App\Models\Vaga;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Inscricao>
 */
class InscricaoFactory extends Factory
{
    protected $model = Inscricao::class;

    public function definition()
    {
        return [
            'cod_inscricao' => Inscricao::generateUniqueCode(),
            'necessita_atendimento' => $this->faker->boolean,
            'qual_atendimento' => $this->faker->optional()->sentence,
        ];
    }

    public function forPessoa(Pessoa $pessoa)
    {
        return $this->state(function (array $attributes) use ($pessoa) {
            return [
                'pessoa_id' => $pessoa->id,
            ];
        });
    }

    public function forProcesso(ProcessoSeletivo $processo)
    {
        return $this->state(function (array $attributes) use ($processo) {
            return [
                'psel_id' => $processo->id,
            ];
        });
    }

    public function forVaga(Vaga $vaga)
    {
        return $this->state(function (array $attributes) use ($vaga) {
            return [
                'vaga_id' => $vaga->id,
            ];
        });
    }
}

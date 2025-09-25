<?php

namespace Database\Factories;

use App\Models\Endereco;
use App\Models\InscricaoPessoa;
use App\Models\Pessoa;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\InscricaoPessoa>
 */
class InscricaoPessoaFactory extends Factory
{
    protected $model = InscricaoPessoa::class;

    /**
     * Define the model's default state.
     *
     * @return array
     */
    public function definition()
    {
        return [
            'nome' => $this->faker->name,
            'nome_social' => $this->faker->optional()->firstName,
            'cpf' => $this->faker->unique()->numerify('###########'),
            'ci' => $this->faker->unique()->numerify('#######'),
            'sexo' => $this->faker->randomElement(['M', 'F']),
            'telefone' => $this->faker->phoneNumber()
        ];
    }
}

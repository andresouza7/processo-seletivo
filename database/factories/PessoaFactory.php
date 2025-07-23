<?php

namespace Database\Factories;

use App\Models\Endereco;
use App\Models\Pessoa;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Pessoa>
 */
class PessoaFactory extends Factory
{
    protected $model = Pessoa::class;

    /**
     * Define the model's default state.
     *
     * @return array
     */
    public function definition()
    {
        $endereco = Endereco::factory()->create();

        return [
            // dados pessoais
            'nome' => $this->faker->name,
            'nome_social' => $this->faker->optional()->firstName,
            'cpf' => $this->faker->unique()->numerify('###########'),
            'sexo' => $this->faker->randomElement(['M', 'F']),
            'telefone_1' => $this->faker->phoneNumber(),

            // endereco
            'endereco_id' => $endereco->id,
        ];
    }
}

<?php

namespace Database\Factories;

use App\Models\Endereco;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Endereco>
 */
class EnderecoFactory extends Factory
{
    protected $model = Endereco::class;

    /**
     * Define the model's default state.
     *
     * @return array
     */
    public function definition()
    {
        return [
            'logradouro' => $this->faker->streetAddress,
            'numero' => $this->faker->buildingNumber,
            'complemento' => $this->faker->optional()->secondaryAddress,
            'bairro' => $this->faker->word,
            'cep' => $this->faker->postcode,
            'uf' => $this->faker->stateAbbr, // Gera uma sigla de estado (UF)
            'cidade' => $this->faker->city,
        ];
    }
}

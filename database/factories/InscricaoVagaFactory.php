<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\InscricaoVaga>
 */
class InscricaoVagaFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'idprocesso_seletivo' => \App\Models\ProcessoSeletivo::factory(),
            'codigo' => $this->faker->unique()->bothify('VAGA-###'),
            'descricao' => $this->faker->sentence(6),
        ];
    }
}

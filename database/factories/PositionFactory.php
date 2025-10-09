<?php

namespace Database\Factories;

use App\Models\Position;
use App\Models\Process;
use Illuminate\Database\Eloquent\Factories\Factory;

class PositionFactory extends Factory
{
    protected $model = Position::class;

    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'process_id' => Process::factory(),
            'code' => $this->faker->unique()->bothify('VAGA-###'),
            'description' => $this->faker->sentence(6),
            'created_at' => now(),
            'updated_at' => now(),
        ];
    }
}

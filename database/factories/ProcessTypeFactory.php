<?php

namespace Database\Factories;

use App\Models\ProcessType;
use Illuminate\Database\Eloquent\Factories\Factory;

class ProcessTypeFactory extends Factory
{
    protected $model = ProcessType::class;

    public function definition(): array
    {
        return [
            'description' => $this->faker->words(2, true), // exemplo: "Edital Público"
            'slug'     => strtoupper($this->faker->lexify('???')), // ex: "ABC"
        ];
    }
}

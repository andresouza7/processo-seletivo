<?php

namespace Database\Factories;

use App\Models\Quota;
use Illuminate\Database\Eloquent\Factories\Factory;

class QuotaFactory extends Factory
{
    protected $model = Quota::class;

    public function definition(): array
    {
        return [
            'description' => $this->faker->sentence(3), // ex: "Vaga administrativa"
        ];
    }
}

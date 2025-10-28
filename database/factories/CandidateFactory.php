<?php

namespace Database\Factories;

use App\Models\Candidate;
use Illuminate\Database\Eloquent\Factories\Factory;

class CandidateFactory extends Factory
{
    protected $model = Candidate::class;

    /**
     * Define the model's default state.
     *
     * @return array
     */
    public function definition()
    {
        return [
            'name' => $this->faker->name,
            'social_name' => $this->faker->optional()->firstName,
            'mother_name' => $this->faker->name('female'),
            'birth_date' => $this->faker->date('Y-m-d', '2005-12-31'),
            'sex' => $this->faker->randomElement(['M', 'F']),
            'rg' => $this->faker->unique()->numerify('#######'),
            'cpf' => $this->faker->unique()->numerify('###########'),
            'gender_identity' => $this->faker->word(),
            'sexual_orientation' => $this->faker->word(),
            'race' => $this->faker->word(),
            'disability' => $this->faker->sentence(2),
            'marital_status' => $this->faker->word(),
            'community' => $this->faker->sentence(2),

            'address' => $this->faker->address(),
            'postal_code' => $this->faker->postcode(),
            'district' => $this->faker->word(),
            'address_number' => $this->faker->buildingNumber(),
            'address_complement' => $this->faker->secondaryAddress(),

            'city' => $this->faker->city(),
            'phone' => $this->faker->phoneNumber(),
            'email' => $this->faker->unique()->safeEmail(),
            'password' => bcrypt('password'),

            'created_at' => now(),
            'updated_at' => now(),
        ];
    }
}

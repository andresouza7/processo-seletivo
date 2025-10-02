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
            'idpessoa' => $this->faker->numberBetween(1, 50000),
            'nome' => $this->faker->name,
            'nome_social' => $this->faker->optional()->firstName,
            'mae' => $this->faker->name('female'),
            'data_nascimento' => $this->faker->date('Y-m-d', '2005-12-31'),
            'sexo' => $this->faker->randomElement(['M', 'F']),
            'ci' => $this->faker->unique()->numerify('#######'),
            'cpf' => $this->faker->unique()->numerify('###########'),
            'matricula' => $this->faker->unique()->numerify('#######'),
            'endereco' => $this->faker->address(),
            'identidade_genero' => $this->faker->randomElement(['C','T','NB','TV','0']),
            'identidade_genero_descricao' => $this->faker->sentence(3),
            'orientacao_sexual' => $this->faker->randomElement(['HT','HM','B','P','A']),
            'raca' => $this->faker->randomElement(['NA', 'NB', 'B', 'I', 'A']),
            'deficiencia' => $this->faker->boolean(),
            'deficiencia_descricao' => $this->faker->sentence(3),
            'estado_civil' => $this->faker->randomElement(['C', 'S', 'D', 'V','U','SP']),
            'comunidade' => $this->faker->randomElement(['R','Q','I','T','O']),
            
            'cep' => $this->faker->postcode(),
            'bairro' => $this->faker->word(),
            'numero' => $this->faker->buildingNumber(),
            'complemento' => $this->faker->secondaryAddress(),

            'cidade' => $this->faker->city(),
            'telefone' => $this->faker->phoneNumber(),
            'email' => $this->faker->unique()->safeEmail(),
            'senha' => bcrypt('password'), 
            'password' => bcrypt('password'),
            'perfil' => 'S',
            'situacao' => 'A',
            'link_lattes' => $this->faker->url(),
            'resumo' => 'RESUMO',
        ];
    }
}

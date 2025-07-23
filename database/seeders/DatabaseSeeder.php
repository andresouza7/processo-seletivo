<?php

namespace Database\Seeders;

use App\Models\Inscricao;
use App\Models\Modalidade;
use App\Models\ProcessoSeletivo;
use App\Models\User;
use App\Models\Vaga;
// use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        // User::factory(10)->create();

        // User::factory()->create([
        //     'name' => 'Test User',
        //     'email' => 'test@example.com',
        // ]);

        $this->call(ProcessoSeletivoSeeder::class);

        $admin = User::factory()->create([
            'email' => 'admin@localhost',
            'password' => Hash::make('admin'),
            'role' => 'admin'
        ]);
        $user = User::factory()->create([
            'email' => 'candidato@localhost',
            'password' => Hash::make('candidato'),
        ]);

        $modalidade = Modalidade::create(['nome' => 'Editais de concurso']);

        $processo_seletivo = ProcessoSeletivo::create([
            'modalidade_id' => $modalidade->id,
            'nome' => 'Prefeitura Tartarugalzinho',
            'numero' => 1,
            'ano' => 2024,
            'status' => 'em_andamento'
        ]);

        Vaga::factory()->create([
            'nome' => 'Professor',
            'psel_id' => $processo_seletivo->id
        ]);
        Vaga::factory()->create([
            'nome' => 'Médico',
            'psel_id' => $processo_seletivo->id
        ]);
        Vaga::factory()->create([
            'nome' => 'Enfermeiro',
            'psel_id' => $processo_seletivo->id
        ]);

        // Create 50 Inscricao instances
        for ($i = 0; $i < 50; $i++) {
            $user = User::factory()->create();
            $vaga = Vaga::inRandomOrder()->first();

            Inscricao::factory()->forProcesso($processo_seletivo)->forPessoa($user->pessoa)->forVaga($vaga)->create();
        }
    }
}

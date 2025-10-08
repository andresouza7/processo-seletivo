<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;

// Models
use App\Models\User;
use App\Models\TipoVaga;
use App\Models\ProcessoSeletivoTipo;
use App\Models\ProcessoSeletivo;
use App\Models\ProcessoSeletivoAnexo;
use App\Models\InscricaoPessoa;
use App\Models\InscricaoVaga;
use App\Models\Inscricao;

class DatabaseSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // ------------------------
        // 1️⃣ Usuários
        // ------------------------
        User::firstOrCreate(
            ['email' => 'admin@example.com'],
            [
                'name' => 'Administrador',
                'password' => Hash::make('admin123'),
                'email_verified_at' => now(),
            ]
        );

        User::factory()->count(10)->create();

        // ------------------------
        // 2️⃣ Tipos de Vagas
        // ------------------------
        $tiposFixos = [
            ['descricao' => 'Ampla Concorrência'],
            ['descricao' => 'Cota Racial'],
            ['descricao' => 'Pessoa com Deficiência'],
            ['descricao' => 'Escola Pública'],
            ['descricao' => 'Interiorização'],
        ];

        foreach ($tiposFixos as $tipo) {
            TipoVaga::firstOrCreate(['descricao' => $tipo['descricao']], $tipo);
        }

        TipoVaga::factory()->count(5)->create();

        // ------------------------
        // 3️⃣ Tipos de Processo Seletivo
        // ------------------------
        $psTiposFixos = [
            ['descricao' => 'Processo Seletivo Simplificado', 'chave' => 'PSS'],
            ['descricao' => 'Concurso Público', 'chave' => 'CON'],
            ['descricao' => 'Edital Interno', 'chave' => 'EDI'],
            ['descricao' => 'Transferência', 'chave' => 'TRA'],
        ];

        foreach ($psTiposFixos as $tipo) {
            ProcessoSeletivoTipo::firstOrCreate(['chave' => $tipo['chave']], $tipo);
        }

        ProcessoSeletivoTipo::factory()->count(3)->create();

        // ------------------------
        // 4️⃣ Pessoas inscritas
        // ------------------------
        InscricaoPessoa::factory()->count(50)->create();

        // ------------------------
        // 5️⃣ Processos Seletivos
        // ------------------------
        $processos = ProcessoSeletivo::factory()
            ->count(5)
            ->withInscricoes(
                qtdVagas: 3,     // 3 vagas por processo
                qtdInscricoes: 10, // 10 inscrições por processo
                qtdFiles: 2       // 2 arquivos por inscrição
            )
            ->create();

        
        // ------------------------
        // 7️⃣ Anexos de Processos Seletivos
        // ------------------------
        foreach ($processos as $processo) {
            ProcessoSeletivoAnexo::factory()
                ->count(rand(1, 3))
                ->create([
                    'idprocesso_seletivo' => $processo->idprocesso_seletivo
                ]);
        }

        // ------------------------
        // 8️⃣ Inscrição Vaga
        // ------------------------
        foreach ($processos as $processo) {
            $vagas = InscricaoVaga::factory()->count(3)->create([
                'idprocesso_seletivo' => $processo->idprocesso_seletivo,
            ]);

            $pessoas = InscricaoPessoa::all();

            // Cria inscrições
            Inscricao::factory()
                ->count(10)
                ->withFiles(2)
                ->sequence(fn($sequence) => [
                    'idprocesso_seletivo' => $processo->idprocesso_seletivo,
                    'idinscricao_vaga' => $vagas->random()->idinscricao_vaga,
                    'idinscricao_pessoa' => $pessoas->random()->idpessoa,
                ])
                ->create();
        }
    }
}

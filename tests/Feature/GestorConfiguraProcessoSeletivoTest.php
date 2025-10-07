<?php

namespace Tests\Feature;

use App\Filament\Gps\Resources\ProcessoSeletivos\Pages\CreateProcessoSeletivo;
use App\Models\ProcessoSeletivo;
use App\Models\ProcessoSeletivoTipo;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Livewire\Livewire;
use Spatie\Permission\Models\Role;
use Tests\TestCase;

class GestorConfiguraProcessoSeletivoTest extends TestCase
{
    use RefreshDatabase;

    protected User $user;
    protected ProcessoSeletivoTipo $tipo;
    protected string $date;

    protected function setUp(): void
    {
        parent::setUp();

        // Cria papel gestor
        Role::create(['name' => 'gestor']);

        // Cria usuário gestor e autentica
        $this->user = User::factory()->create([
            'name' => 'tester gps',
            'email' => 'gestorps@admin.com.br',
        ]);
        $this->user->assignRole('gestor');
        $this->actingAs($this->user);

        // Cria tipo de processo
        $this->tipo = ProcessoSeletivoTipo::factory()->create();

        // Define data padrão
        $this->date = now()->toDateString();

        // Acessa a página antes de cada teste (opcional)
        $this->get(route('filament.gps.resources.processos.create'))->assertStatus(200);
    }

    public function test_valida_campos_processo_seletivo(): void
    {
        Livewire::test(CreateProcessoSeletivo::class)
            ->assertSchemaExists('form')
            ->fillForm([])
            ->call('create')
            ->assertHasFormErrors(); // valida que erros são gerados quando dados obrigatórios não são preenchidos
    }

    public function test_gestor_cria_processo_seletivo(): void
    {
        $formData = [
            'titulo' => 'Edital Monitoria',
            'idprocesso_seletivo_tipo' => $this->tipo->idprocesso_seletivo_tipo,
            'numero' => '01/2025',
            'data_criacao' => $this->date,
            'publicado' => 'S',
            'descricao' => 'lorem ipsum',
            'data_publicacao_inicio' => $this->date,
            'data_publicacao_fim' => $this->date,
            'data_inscricao_inicio' => $this->date,
            'data_inscricao_fim' => $this->date,
            'possui_isencao' => false,
            'anexos' => [
                ['item' => 'identidade']
            ],
        ];

        Livewire::test(CreateProcessoSeletivo::class)
            ->assertSchemaExists('form')
            ->fillForm($formData)
            ->call('create')
            ->assertHasNoFormErrors();

        $processo = ProcessoSeletivo::latest()->first();

        // Valida campos do banco
        $this->assertDatabaseHas('processo_seletivo', [
            'titulo' => $formData['titulo'],
            'idprocesso_seletivo_tipo' => $formData['idprocesso_seletivo_tipo'],
            'numero' => $formData['numero'],
            'data_criacao' => $this->date,
            'publicado' => 'S',
            'descricao' => '<p>lorem ipsum</p>', // Filament salva HTML
            'data_publicacao_inicio' => $this->date,
            'data_publicacao_fim' => $this->date,
            'data_inscricao_inicio' => $this->date,
            'data_inscricao_fim' => $this->date,
            'possui_isencao' => 0,
        ]);

        // Valida anexos
        $this->assertEquals(
            $formData['anexos'],
            $processo->anexos
        );
    }

    public function test_gestor_pode_alterar_processo_seletivo(): void
    {
        // 1️⃣ Cria um processo seletivo existente
        $processo = ProcessoSeletivo::factory()->create([
            'titulo' => 'Processo Original',
            'descricao' => '<p>Texto original</p>',
            'idprocesso_seletivo_tipo' => $this->tipo->idprocesso_seletivo_tipo,
            'numero' => '02/2025',
            'data_criacao' => $this->date,
            'publicado' => 'N',
            'possui_isencao' => false,
        ]);

        // 2️⃣ Define os novos dados que o gestor vai alterar
        $novosDados = [
            'titulo' => 'Processo Atualizado',
            'descricao' => 'Descrição atualizada pelo gestor',
            'publicado' => 'S',
        ];

        // 3️⃣ Simula o componente de edição (Filament Edit Page)
        Livewire::test(\App\Filament\Gps\Resources\ProcessoSeletivos\Pages\EditProcessoSeletivo::class, [
            'record' => $processo->getKey(),
        ])
            ->fillForm($novosDados)
            ->call('save')
            ->assertHasNoFormErrors();

        // 4️⃣ Valida se as alterações foram persistidas no banco
        $this->assertDatabaseHas('processo_seletivo', [
            'idprocesso_seletivo' => $processo->idprocesso_seletivo,
            'titulo' => $novosDados['titulo'],
            'descricao' => '<p>' . $novosDados['descricao'] . '</p>',
            'publicado' => 'S',
        ]);
    }
}

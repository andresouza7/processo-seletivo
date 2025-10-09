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
            'title' => 'Edital Monitoria',
            'idprocesso_seletivo_tipo' => $this->tipo->idprocesso_seletivo_tipo,
            'number' => '01/2025',
            'document_date' => $this->date,
            'is_published' => 'S',
            'description' => 'lorem ipsum',
            'publication_start_date' => $this->date,
            'publication_end_date' => $this->date,
            'application_start_date' => $this->date,
            'application_end_date' => $this->date,
            'has_fee_exemption' => false,
            'attachment_fields' => [
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
            'title' => $formData['title'],
            'idprocesso_seletivo_tipo' => $formData['idprocesso_seletivo_tipo'],
            'number' => $formData['number'],
            'document_date' => $this->date,
            'is_published' => 'S',
            'description' => '<p>lorem ipsum</p>', // Filament salva HTML
            'publication_start_date' => $this->date,
            'publication_end_date' => $this->date,
            'application_start_date' => $this->date,
            'application_end_date' => $this->date,
            'has_fee_exemption' => 0,
        ]);

        // Valida anexos
        $this->assertEquals(
            $formData['attachment_fields'],
            $processo->attachment_fields
        );
    }

    public function test_gestor_pode_alterar_processo_seletivo(): void
    {
        // 1️⃣ Cria um processo seletivo existente
        $processo = ProcessoSeletivo::factory()->create([
            'title' => 'Processo Original',
            'description' => '<p>Texto original</p>',
            'idprocesso_seletivo_tipo' => $this->tipo->idprocesso_seletivo_tipo,
            'number' => '02/2025',
            'document_date' => $this->date,
            'is_published' => 'N',
            'has_fee_exemption' => false,
            'attachment_fields' => [
                ['item' => 'identidade']
            ],
        ]);

        // 2️⃣ Define os novos dados que o gestor vai alterar
        $novosDados = [
            'title' => 'Processo Atualizado',
            'description' => 'Descrição atualizada pelo gestor',
            'is_published' => 'S',
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
            'title' => $novosDados['title'],
            'description' => '<p>' . $novosDados['description'] . '</p>',
            'is_published' => 'S',
        ]);
    }
}

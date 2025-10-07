<?php

namespace Tests\Feature;

use App\Filament\Candidato\Resources\Inscricaos\Pages\CreateInscricao;
use App\Models\Inscricao;
use App\Models\InscricaoPessoa;
use App\Models\InscricaoVaga;
use App\Models\ProcessoSeletivo;
use App\Models\TipoVaga;
use Filament\Facades\Filament;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Livewire\Livewire;
use Tests\TestCase;

class CandidatoRealizaNovaInscricaoTest extends TestCase
{
    use RefreshDatabase;

    protected InscricaoPessoa $user;
    protected ProcessoSeletivo $processo;
    protected InscricaoVaga $vaga;
    protected TipoVaga $tipo;

    protected function setUp(): void
    {
        parent::setUp();

        Filament::setCurrentPanel('candidato');

        // Cria dados comuns para os testes
        $this->user = InscricaoPessoa::factory()->create();
        $this->actingAs($this->user, 'candidato');
        Livewire::actingAs($this->user, 'candidato');


        $this->processo = ProcessoSeletivo::factory()->create();
        $this->vaga = InscricaoVaga::factory()->create([
            'idprocesso_seletivo' => $this->processo->idprocesso_seletivo,
        ]);

        TipoVaga::factory()->create(['id_tipo_vaga' => 1]);
        TipoVaga::factory()->create(['id_tipo_vaga' => 3]);

        // Carrega a página primeiro para inicializar o painel do Filament
        $response = $this->actingAs($this->user, 'candidato')->get(route('filament.candidato.resources.inscricoes.create'));
        $response->assertStatus(200);
    }

    public function test_user_cannot_submit_form_without_accepting_terms(): void
    {
        Livewire::test(CreateInscricao::class)
            ->fillForm([
                'idprocesso_seletivo'   => $this->processo->idprocesso_seletivo,
                'idinscricao_vaga'       => $this->vaga->idinscricao_vaga,
                'necessita_atendimento'  => 'N',
                'pcd'                    => false,
                // 'aceita_termos' não enviado → simula checkbox desmarcado
            ])
            ->call('create')
            ->assertHasFormErrors(['aceita_termos' => 'accepted']);
    }

    public function test_user_can_submit_form_when_accepting_terms(): void
    {

        Livewire::test(CreateInscricao::class)
            ->fillForm([
                'idprocesso_seletivo'   => $this->processo->idprocesso_seletivo,
                'idinscricao_vaga'       => $this->vaga->idinscricao_vaga,
                'necessita_atendimento'  => 'N',
                'pcd'                    => false,
                'aceita_termos'          => true, // checkbox marcado
            ])
            ->call('create')
            ->assertHasNoFormErrors();

        // Verifica se o registro foi salvo no banco
        $this->assertDatabaseHas('inscricao', [
            'idinscricao_pessoa'     => $this->user->idpessoa,
            'idprocesso_seletivo'    => $this->processo->idprocesso_seletivo,
            'idinscricao_vaga'       => $this->vaga->idinscricao_vaga,
            'necessita_atendimento'  => 'N',
        ]);
    }

    public function test_user_can_submit_form_with_necessita_atendimento(): void
    {
        $qualAtendimento = 'Mesa adaptada';

        Livewire::test(CreateInscricao::class)
            ->fillForm([
                'idprocesso_seletivo'   => $this->processo->idprocesso_seletivo,
                'idinscricao_vaga'       => $this->vaga->idinscricao_vaga,
                'necessita_atendimento' => 'S',
                'qual_atendimento'       => $qualAtendimento,
                'pcd'                    => false,
                'aceita_termos'          => true,
            ])
            ->call('create')
            ->assertHasNoFormErrors();

        // Verifica se o registro foi salvo corretamente no banco
        $this->assertDatabaseHas('inscricao', [
            'idinscricao_pessoa'     => $this->user->idpessoa,
            'idprocesso_seletivo'    => $this->processo->idprocesso_seletivo,
            'idinscricao_vaga'       => $this->vaga->idinscricao_vaga,
            'necessita_atendimento'  => 'S',
            'qual_atendimento'       => $qualAtendimento,
        ]);
    }

    public function test_requer_selecao_de_vaga(): void
    {
        Livewire::test(CreateInscricao::class)
            ->fillForm([
                'idprocesso_seletivo'   => $this->processo->idprocesso_seletivo,
                'necessita_atendimento' => 'N',
                'pcd'                    => false,
                'aceita_termos'          => true,
            ])
            ->call('create')
            ->assertHasFormErrors(['idinscricao_vaga' => 'required']);
    }

    public function test_user_cannot_submit_without_laudo_medico(): void
    {
        Storage::fake('media');

        Livewire::test(CreateInscricao::class)
            ->fillForm([
                'idprocesso_seletivo' => $this->processo->idprocesso_seletivo,
                'idinscricao_vaga'    => $this->vaga->idinscricao_vaga,
                'necessita_atendimento' => 'N',
                'pcd'                 => true,
                'aceita_termos'       => true,
            ])
            ->call('create')
            ->assertHasFormErrors(['laudo_medico' => 'required']);
    }

    ///////////// aguardando FIX
    ////////////////////////////

    public function test_user_can_upload_valid_pdf(): void
    {
        Storage::fake('media');

        $file = UploadedFile::fake()->createWithContent('documento.pdf', '%PDF-1.4 fake content here')->size(1024);

        Livewire::test(CreateInscricao::class)
            ->fillForm([
                'idprocesso_seletivo' => $this->processo->idprocesso_seletivo,
                'idinscricao_vaga'    => $this->vaga->idinscricao_vaga,
                'necessita_atendimento' => 'N',
                'pcd'                 => true,
                'aceita_termos'       => true,
                'laudo_medico'        => $file,
            ])
            ->call('create')
            ->assertHasNoFormErrors();

        $inscricao = Inscricao::latest()->first();
        $mediaItem = $inscricao->getFirstMedia('laudo_medico');

        $this->assertNotNull($mediaItem);

        $customPath = (new \App\Services\MediaLibrary\CustomPathGenerator())->getPath($mediaItem) . $mediaItem->file_name;
        Storage::disk($mediaItem->disk)->assertExists($customPath);

        $this->assertEquals('application/pdf', $mediaItem->mime_type);
        $this->assertLessThanOrEqual(2 * 1024 * 1024, $mediaItem->size);
    }

    public function test_user_cannot_upload_invalid_file(): void
    {
        Storage::fake('media');

        /** @var \App\Models\InscricaoPessoa|\Illuminate\Contracts\Auth\Authenticatable $user */
        $user = InscricaoPessoa::factory()->createOne();
        $this->actingAs($user, 'candidato');

        $processo = ProcessoSeletivo::factory()->create();
        $vaga = InscricaoVaga::factory()->create();

        $invalidFile = UploadedFile::fake()->createWithContent(
            'documento.docx',
            'PK fake content for docx'
        )->size(3000);

        Livewire::test(CreateInscricao::class)
            ->fillForm([
                'idprocesso_seletivo' => $processo->idprocesso_seletivo,
                'idinscricao_vaga'    => $vaga->idinscricao_vaga,
                'necessita_atendimento' => 'N',
                'pcd'                 => true,
                'aceita_termos'       => true,
                'laudo_medico'        => $invalidFile,
            ])
            ->call('create')
            ->assertHasFormErrors(['laudo_medico']);
    }
}

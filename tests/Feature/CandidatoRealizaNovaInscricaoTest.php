<?php

namespace Tests\Feature;

use App\Filament\Candidato\Resources\Inscricaos\Pages\CreateInscricao;
use App\Models\Application;
use App\Models\Candidate;
use App\Models\Position;
use App\Models\Process;
use App\Models\Quota;
use Filament\Facades\Filament;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Livewire\Livewire;
use Tests\TestCase;

class CandidatoRealizaNovaInscricaoTest extends TestCase
{
    use RefreshDatabase;

    protected Candidate $user;
    protected Process $processo;
    protected Position $vaga;
    protected Quota $type;

    protected function setUp(): void
    {
        parent::setUp();

        Filament::setCurrentPanel('candidato');

        // Cria dados comuns para os testes
        $this->user = Candidate::factory()->create();
        $this->actingAs($this->user, 'candidato');
        Livewire::actingAs($this->user, 'candidato');


        $this->processo = Process::factory()->create();
        $this->vaga = Position::factory()->create([
            'id' => $this->processo->id,
        ]);

        Quota::factory()->create(['id' => 1]);
        Quota::factory()->create(['id' => 3]);

        // Carrega a página primeiro para inicializar o painel do Filament
        $response = $this->actingAs($this->user, 'candidato')->get(route('filament.candidato.resources.inscricoes.create'));
        $response->assertStatus(200);
    }

    public function test_user_cannot_submit_form_without_accepting_terms(): void
    {
        Livewire::test(CreateInscricao::class)
            ->fillForm([
                'id'   => $this->processo->id,
                'idinscricao_vaga'       => $this->vaga->idinscricao_vaga,
                'requires_assistance'  => 'N',
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
                'id'   => $this->processo->id,
                'idinscricao_vaga'       => $this->vaga->idinscricao_vaga,
                'requires_assistance'  => 'N',
                'pcd'                    => false,
                'aceita_termos'          => true, // checkbox marcado
            ])
            ->call('create')
            ->assertHasNoFormErrors();

        // Verifica se o registro foi salvo no banco
        $this->assertDatabaseHas('inscricao', [
            'idinscricao_pessoa'     => $this->user->id,
            'id'    => $this->processo->id,
            'idinscricao_vaga'       => $this->vaga->idinscricao_vaga,
            'requires_assistance'  => 'N',
        ]);
    }

    public function test_user_can_submit_form_with_necessita_atendimento(): void
    {
        $qualAtendimento = 'Mesa adaptada';

        Livewire::test(CreateInscricao::class)
            ->fillForm([
                'id'   => $this->processo->id,
                'idinscricao_vaga'       => $this->vaga->idinscricao_vaga,
                'requires_assistance' => 'S',
                'assistance_details'       => $qualAtendimento,
                'pcd'                    => false,
                'aceita_termos'          => true,
            ])
            ->call('create')
            ->assertHasNoFormErrors();

        // Verifica se o registro foi salvo corretamente no banco
        $this->assertDatabaseHas('inscricao', [
            'idinscricao_pessoa'     => $this->user->id,
            'id'    => $this->processo->id,
            'idinscricao_vaga'       => $this->vaga->idinscricao_vaga,
            'requires_assistance'  => 'S',
            'assistance_details'       => $qualAtendimento,
        ]);
    }

    public function test_requer_selecao_de_vaga(): void
    {
        Livewire::test(CreateInscricao::class)
            ->fillForm([
                'id'   => $this->processo->id,
                'requires_assistance' => 'N',
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
                'id' => $this->processo->id,
                'idinscricao_vaga'    => $this->vaga->idinscricao_vaga,
                'requires_assistance' => 'N',
                'pcd'                 => true,
                'aceita_termos'       => true,
            ])
            ->call('create')
            ->assertHasFormErrors(['laudo_medico' => 'required']);
    }


    public function test_user_can_upload_valid_pdf(): void
    {
        Storage::fake('media');

        $file = UploadedFile::fake()->createWithContent('documento.pdf', '%PDF-1.4 fake content here')->size(1024);

        Livewire::test(CreateInscricao::class)
            ->fillForm([
                'id' => $this->processo->id,
                'idinscricao_vaga'    => $this->vaga->idinscricao_vaga,
                'requires_assistance' => 'N',
                'pcd'                 => true,
                'aceita_termos'       => true,
                'laudo_medico'        => $file,
            ])
            ->call('create')
            ->assertHasNoFormErrors();

        $application = Application::latest()->first();
        $mediaItem = $application->getFirstMedia('laudo_medico');

        $this->assertNotNull($mediaItem);

        $customPath = (new \App\Services\MediaLibrary\CustomPathGenerator())->getPath($mediaItem) . $mediaItem->file_name;
        Storage::disk($mediaItem->disk)->assertExists($customPath);

        $this->assertEquals('application/pdf', $mediaItem->mime_type);
        $this->assertLessThanOrEqual(2 * 1024 * 1024, $mediaItem->size);
    }

    public function test_user_cannot_upload_invalid_file(): void
    {
        Storage::fake('media');

        /** @var \App\Models\Candidate|\Illuminate\Contracts\Auth\Authenticatable $user */
        $user = Candidate::factory()->createOne();
        $this->actingAs($user, 'candidato');

        $processo = Process::factory()->create();
        $vaga = Position::factory()->create();

        $invalidFile = UploadedFile::fake()->createWithContent(
            'documento.docx',
            'PK fake content for docx'
        )->size(3000);

        Livewire::test(CreateInscricao::class)
            ->fillForm([
                'id' => $processo->id,
                'idinscricao_vaga'    => $vaga->idinscricao_vaga,
                'requires_assistance' => 'N',
                'pcd'                 => true,
                'aceita_termos'       => true,
                'laudo_medico'        => $invalidFile,
            ])
            ->call('create')
            ->assertHasFormErrors(['laudo_medico']);
    }
}

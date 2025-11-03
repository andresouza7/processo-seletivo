<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Hash;
use Tests\TestCase;
use App\Models\Candidate;
use Illuminate\Support\Facades\Auth;

use function PHPUnit\Framework\assertTrue;

class CandidatoRealizaLoginTest extends TestCase
{
    use RefreshDatabase;

    public function test_candidato_faz_login(): void
    {
        // CRIA O USUÁRIO CORRETAMENTE
        $user = Candidate::factory()->create([
            'name' => 'tester',
            'cpf' => '12345678901',
            'password' => Hash::make('pwd'),
        ]);

        $successfulLogin = Auth::guard('candidato')->attempt(['cpf' => '12345678901', 'password' => 'pwd']);

        // LOGA PELO GUARD CORRETO
        $this->actingAs($user, 'candidato');

        // Usa a rota FILAMENT diretamente via URL ou route
        $response = $this->get(route('filament.candidato.pages.meus-dados'));

        // ASSERT DE SUCESSO
        $response->assertStatus(200);
        $response->assertSee('tester');
        assertTrue($successfulLogin);
    }
}

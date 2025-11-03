<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Hash;
use Tests\TestCase;
use App\Models\User;
use Illuminate\Support\Facades\Auth;

use function PHPUnit\Framework\assertTrue;

class GestorRealizaLoginTest extends TestCase
{
    use RefreshDatabase;

    public function test_gestor_processo_seletivo_faz_login_com_email_e_senha(): void
    {
        // CRIA O USUÁRIO CORRETAMENTE
        $user = User::factory()->create([
            'name' => 'tester gps',
            'email' => 'gestorps@admin.com.br',
            'password' => Hash::make('pwd'),
        ]);

        $successfulLogin = Auth::guard('web')->attempt(['email' => 'gestorps@admin.com.br', 'password' => 'pwd']);
        assertTrue($successfulLogin);

        // LOGA PELO GUARD CORRETO
        $this->actingAs($user, 'web'); //EXPLÍCITO, mas é o comportamento padrão.

        // Usa a rota FILAMENT diretamente via URL ou route
        $response = $this->get(route('filament.gps.pages.dashboard'));

        // ASSERT DE SUCESSO
        $response->assertStatus(200);
        $response->assertSee('tester gps');
        //CHECA PARA VER SE ENTROU EM PAINEL DE CONTROLE
        $response->assertSee('Painel de Controle');
    }
}

<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase; 
use App\Filament\Pages\MeusDados;   // caminho da página
use App\Models\InscricaoPessoa;                // caminho do modelo2
use Spatie\Permission\Models\Role;  // role


class dadospessoais_bloqueia_alteracao_Test extends TestCase
{
    
    public function test_bloqueia_alteracao_de_dados(): void
    {

        // CRIA E POPULA $PESSOA 
        $user = InscricaoPessoa::factory()->create([
        ]);
           
        // Acessa a rota da página
        $response = $this->get('/candidato/meus-dados');

        // Verifica se a página carregou corretamente
        $response->assertStatus(200);

        // Verifica se os campos desabilitados aparecem no HTML como readonly ou disabled
        $response->assertSee('name="nome" disabled');

    }
}

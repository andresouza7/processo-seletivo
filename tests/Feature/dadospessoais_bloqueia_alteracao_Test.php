<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase; 
use App\Filament\Pages\MeusDados;   // caminho da página
use App\Models\InscricaoPessoa;     // caminho do modelo2
use Illuminate\Support\Facades\Auth;
use Spatie\Permission\Models\Role;  // role


class dadospessoais_bloqueia_alteracao_Test extends TestCase
{
    
    public function test_bloqueia_alteracao_de_dados(): void
    {

        
        // 1️⃣ Criar um candidato com ID
        $inscricao = InscricaoPessoa::factory()->create([
        ]);

        //echos
        echo "nome: ", $inscricao->nome, " ";
        echo "email: ", $inscricao->email, " ";
        echo "senha: ",$inscricao->senha, " ";
        echo "idpessoa: ",$inscricao->idpessoa, " ";

        // 2️⃣ Autenticar o candidato
        $this->actingAs($inscricao, 'candidato');
        
        // 3️⃣ Executar a ação que você quer testar
        $user = InscricaoPessoa::where('idpessoa', Auth::guard('candidato')->id())
                                ->firstOrFail();
                                

        // Acessa a rota da página
        $response = $this->get(route('filament.candidato.pages.meus-dados'));

        // Verifica se a página carregou corretamente
        $response->assertStatus(200);

        // Verifica se os campos desabilitados aparecem no HTML como readonly ou disabled
        $response->assertSee('name="nome" disabled');

    }
}

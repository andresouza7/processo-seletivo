<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
       Schema::create('inscricao', function (Blueprint $table) {
            $table->id('idinscricao');
            $table->string('cod_inscricao', 32);
            
            $table->foreignId('idprocesso_seletivo')->constrained('processo_seletivo', 'idprocesso_seletivo');
            $table->foreignId('idinscricao_vaga')->constrained('inscricao_vagas', 'idinscricao_vaga');
            $table->foreignId('idinscricao_pessoa')->constrained('inscricao_pessoa', 'idpessoa');
            $table->foreignId('idtipo_vaga')->nullable()->constrained('tipo_vaga', 'id_tipo_vaga');
            
            $table->timestamp('data_hora')->useCurrent()->useCurrentOnUpdate();
            
            $table->char('necessita_atendimento', 1);
            $table->longText('qual_atendimento')->nullable();
            $table->longText('observacao')->nullable();
            $table->longText('local_prova')->nullable();
            $table->string('ano_enem', 255)->nullable();
            $table->char('bonificacao', 1)->nullable();
            
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('inscricao');
    }
};

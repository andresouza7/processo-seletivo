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
        Schema::create('recursos', function (Blueprint $table) {
            $table->increments('idrecurso');
            $table->longText('descricao')->nullable();
            $table->longText('resposta')->nullable();
            $table->foreignId('idprocesso_seletivo')->constrained('processo_seletivo', 'idprocesso_seletivo');
            $table->foreignId('idinscricao_pessoa')->constrained('inscricao_pessoa', 'idpessoa');
            $table->foreignId('idinscricao')->nullable()->constrained('inscricao', 'idinscricao');
            $table->char('situacao', 2)->nullable();
            $table->timestamp('data_hora')->useCurrent()->useCurrentOnUpdate();
            $table->foreignId('idetapa_recurso')->constrained('etapa_recurso', 'idetapa_recurso');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('recursos');
    }
};

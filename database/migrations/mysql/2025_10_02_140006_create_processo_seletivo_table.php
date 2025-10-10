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
        Schema::create('processo_seletivo', function (Blueprint $table) {
            $table->id('idprocesso_seletivo');
            $table->foreignId('idprocesso_seletivo_tipo')->constrained('processo_seletivo_tipo', 'idprocesso_seletivo_tipo');
            $table->string('titulo', 255)->nullable();
            $table->longText('descricao')->nullable();
            $table->string('numero', 20);
            $table->date('data_criacao');
            $table->char('situacao', 1);
            $table->integer('acessos');
            $table->char('publicado', 1);
            $table->string('diretorio', 30);
            $table->date('data_publicacao_inicio')->nullable();
            $table->date('data_publicacao_fim')->nullable();
            $table->date('data_inscricao_inicio')->nullable();
            $table->date('data_inscricao_fim')->nullable();
            $table->date('data_recurso_inicio')->nullable();
            $table->date('data_recurso_fim')->nullable();
            $table->boolean('possui_isencao')->default(0);
            $table->json('anexos')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('processo_seletivo');
    }
};

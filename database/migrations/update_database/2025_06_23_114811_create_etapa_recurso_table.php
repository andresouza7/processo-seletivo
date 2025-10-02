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
        Schema::create('etapa_recurso', function (Blueprint $table) {
            $table->id('idetapa_recurso');

            $table->text('descricao');
            $table->date('data_inicio_recebimento');
            $table->date('data_fim_recebimento');
            $table->date('data_inicio_resultado');
            $table->date('data_fim_resultado');
            $table->boolean('requer_anexos')->default(false);
            $table->boolean('permite_multiplos_recursos')->default(false);

            // Adiciona a coluna de chave estrangeira
            $table->integer('idprocesso_seletivo')->nullable();

            // Define a chave estrangeira
            $table->foreign('idprocesso_seletivo')
                  ->references('idprocesso_seletivo')
                  ->on('processo_seletivo')
                  ->onDelete('cascade');

            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('etapa_recurso');
    }
};

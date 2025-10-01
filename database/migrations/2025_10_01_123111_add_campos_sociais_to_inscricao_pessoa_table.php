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
        Schema::table('inscricao_pessoa', function (Blueprint $table) {
            // checkbox (boolean)
            $table->boolean('deficiencia')->default(false)->after('orientacao_sexual');

            // select raça (string, código NA, NB, B, I, A)
            $table->string('raca', 2)->nullable()->after('deficiencia');

            // textbox descrição (string)
            $table->string('deficiencia_descricao')->nullable()->after('raca');

            // select estado civil (string, código C, S, D, etc.)
            $table->string('estado_civil', 2)->nullable()->after('deficiencia_descricao');

            // select comunidade (string, código R, Q, I, T, O)
            $table->string('comunidade', 2)->nullable()->after('estado_civil');
        });
    }

    public function down(): void
    {
        Schema::table('inscricao_pessoa', function (Blueprint $table) {
            $table->dropColumn([
                'deficiencia',
                'raca',
                'deficiencia_descricao',
                'estado_civil',
                'comunidade',
            ]);
        });
    }
};
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
        Schema::create('processo_seletivo_tipo', function (Blueprint $table) {
            $table->id('idprocesso_seletivo_tipo');
            $table->string('descricao', 50);
            $table->string('chave', 10);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('processo_seletivo_tipo');
    }
};

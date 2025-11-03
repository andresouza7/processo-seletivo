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
        Schema::table('recursos', function (Blueprint $table) {
            $table->foreignId('idetapa_recurso')->nullable()
                ->constrained('etapa_recurso', 'idetapa_recurso')
                ->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('recursos', function (Blueprint $table) {
            $table->dropForeign(['idetapa_recurso']);
            $table->dropColumn('idetapa_recurso');
        });
    }
};

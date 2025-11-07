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
        Schema::create('form_fields', function (Blueprint $table) {
            $table->id();
            $table->foreignId('process_id')->constrained();
            $table->string('label');          // Ex: "CPF", "Título do artigo"
            $table->string('name');           // Ex: "cpf", "titulo_artigo"
            $table->string('type');           // Ex: text, textarea, file, select, checkbox, number, date, etc.
            $table->boolean('required')->default(false);
            $table->json('options')->nullable(); // Ex: ["Sim","Não"] para selects
            $table->integer('order')->default(0);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('form_fields');
    }
};

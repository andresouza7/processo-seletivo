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
        Schema::create('processes', function (Blueprint $table) {
            $table->id();
            $table->foreignId('process_type_id')->constrained();
            $table->text('title');
            $table->text('description');
            $table->string('number', 20);
            $table->string('directory', 20);
            $table->unsignedBigInteger('views')->default(0);
            $table->boolean('is_published');
            $table->date('publication_start_date');
            $table->date('publication_end_date');
            $table->date('application_start_date');
            $table->date('application_end_date');
            $table->boolean('has_fee_exemption')->default(false);
            $table->jsonb('attachment_fields')->nullable();
            $table->boolean('single_application')->default(false);
            $table->timestamps();
            $table->softDeletes();

            // Composite unique key
            $table->unique(['number', 'process_type_id']);
            $table->unique(['directory', 'process_type_id']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('processes');
    }
};

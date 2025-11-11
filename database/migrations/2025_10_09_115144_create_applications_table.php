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
        Schema::create('applications', function (Blueprint $table) {
            $table->id();
            $table->string('code', 32)->unique();
            $table->foreignId('candidate_id')->constrained();
            $table->foreignId('position_id')->constrained();
            $table->foreignId('process_id')->constrained();
            $table->foreignId('quota_id')->nullable()->constrained();
            $table->boolean('requires_assistance')->default(false);
            $table->text('assistance_details')->nullable();
            $table->jsonb('form_data')->nullable();
            $table->timestamps();
            $table->softDeletes();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('applications');
    }
};
